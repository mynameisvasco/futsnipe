<?php
 
namespace App\Console\Commands;
 
use Illuminate\Console\Command;
use App\Transaction;
use App\Item;
use App\Account;
use App\Configuration;
use App\Helpers;
use App\User;
use FUTApi\Core;
use FUTApi\FutError;
use Carbon\Carbon;
use Artisan;
use Log;
use App\Stats;
use App\FifaCard;
use Telegram;
 
class SnipePlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snipeplayers:cron {account_id}';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command start the snipe bot';
 
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
 
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $stats = Stats::whereDate('created_at' , '=', Carbon::today()->toDateString())->first();

        //Check if today's stats record is on database if not create it
        if(empty($stats))
        {
            Log::info("test");
            $stats = new Stats();
            $stats->coins_balance = 0;
            $stats->total_transactions = 0;
            $stats->save();
            $stats = Stats::whereDate('created_at' , '=', Carbon::today()->toDateString())->first();
        }
        
        $account = Account::where('id', $this->argument('account_id'))->first();
        $configuration = Configuration::where('user_id', $account->user_id)->first();
        $user = User::find($account->user_id);

        if(!isset($account)) $this->error("No account found with id " + $this->argument('account_id'));

        //Check if accounts needs to cooldown
        if($account->minutesRunning >= $configuration->snipe_cooldown)
        {
            //Set status to stopped if not stopped
            if($account->status != 3)
            {
                $account->status = 3;
                $account->save();
                $this->info($account->email . ' is now in cooldown for ' . $configuration->snipe_cooldown . ' minutes');
                Log::info($account->email . ' is now in cooldown for ' . $configuration->snipe_cooldown . ' minutes');
                die();
            }
            //If cooldown time passed start again
            elseif($account->minutesRunning > $configuration->snipe_cooldown * 2)
            {
                //Reset the minutesRunning timer
                $account->minutesRunning = 0;
                $account->status = 2;
                $account->save();
                $this->info($account->email . ' cooldown time is over ');
                Log::info($account->email . ' cooldown time is over');
                die();
            }
        }
        else
        {
            //Try to use saved cookies to login, if not possible refresh session
            try
            {
                $fut = new Core(
                    $account->email,
                    $account->password,
                    $account->platform,
                    null,
                    false,
                    false,
                    storage_path(
                        'app/fut_cookies/' . md5($account->email)
                    ));

                $fut->setSession(
                    $account->personaId,
                    $account->nucleusId,
                    $account->phishingToken,
                    $account->sessionId,
                    date("Y-m", strtotime($account->dob))
                );
            }
            catch(FutError $e)
            {
                $error = $e->GetOptions();

                $account->phishingToken = null;
                $account->sessionId = null;
                $account->nucleusId = null;
                $account->personaId = null;
                $account->clubName = null;
                $account->coins = null;
                $account->dob = null;
                $account->tradepile_limit = null;
                $account->last_login = null;
                $account->status = -1;
                $account->status_reason = $error['reason'];
                $account->save();
                if($configuration->telegram_channel != "")
                {
                    Telegram::sendMessage([
                        'chat_id' => '@'.$configuration->telegram_channel, 
                        'text' => 'We have this error on '. $account->email . ' account: ' . $error['reason']
                    ]);
                }
                $this->error("We have an error logging in: ".$error['reason']);
                Log::error("We have an error logging in: ".$error['reason']);
                die();
            }

            $startedTime = Carbon::now();

            //Check Trade pile
            try
            {
                $tradepile = $fut->tradepile();
            }
            catch(FutError $e)
            {
                Artisan::call('accounts:cron ' . $account->id);
                die();
            }

            $tradepile = json_decode(json_encode($tradepile));
            foreach($tradepile->auctionInfo as $item)
            {
                //If trade is closed mark as sold
                if($item->tradeState == 'closed')
                {
                    try
                    {
                        $fut->removeSold($item->tradeId);
                    }
                    catch(FutError $e)
                    {
                        $error = $e->GetOptions();
                        if($error['reason'] == 'permission_denied') die();
                        $account->status_reason = $error['reason'];
                        $account->status = -1;
                        $account->save();
                        if($configuration->telegram_channel != "")
                        {
                            Telegram::sendMessage([
                                'chat_id' => '@'.$configuration->telegram_channel, 
                                'text' => 'We have this error on '. $account->email . ' account: ' . $error['reason']
                            ]);
                        }
                        die();
                    }
                    
                    //Check if player card is on the database
                    $fifacard = FifaCard::where('definition_id', $item->itemData->resourceId)->first();
                    //If not add it
                    if(empty($fifacard))
                    {
                        $queryCard = json_decode(file_get_contents('https://www.easports.com/fifa/ultimate-team/api/fut/item?jsonParamObject&id=' . $item->itemData->resourceId));
                        if($queryCard->items[0]->commonName != "")
                        {
                            $name = $queryCard->items[0]->commonName;
                        }
                        else
                        {
                            $name = $queryCard->items[0]->lastName;
                        }
                        $fifacard = new FifaCard();
                        $fifacard->rating = $item->itemData->rating;
                        $fifacard->type = Helpers::getCardType($item->itemData->rating, $item->itemData->rareflag);
                        $fifacard->name = $name;
                        $fifacard->position = $item->itemData->preferredPosition;
                        $fifacard->club = $item->itemData->teamid;
                        $fifacard->nationality = $item->itemData->nation;
                        $fifacard->asset_id = $item->itemData->assetId;
                        $fifacard->definition_id = $item->itemData->resourceId;
                        $fifacard->save();
                    }


                    //Save the transaction in database and update account coins
                    $transaction = new Transaction();
                    $transaction->definition_id = $item->itemData->resourceId;
                    $transaction->coins = $item->buyNowPrice;
                    $transaction->type = "Sell";
                    $transaction->account_id = $account->id;
                    $transaction->save();

                    //Update today's stats
                    $stats->coins_balance += $item->buyNowPrice;
                    $stats->total_transactions += 1;
                    $stats->save();

                    $account->coins += $item->buyNowPrice;
                    $account->save();
                }
            }            
            //Get all user snipe items
            $items = Item::where('user_id', $user->id)->get();
            
            //If there is no items to snipe
            if(count($items) == 0)
            {
                Log::info('No items to snipe on ' . $account->email);
                die();
            }

            $requestsDone = 0;
            while($requestsDone < $configuration->rpm)
            {
                //For each item in database that belongs to current user
                foreach($items as $item)
                {
                    //Check if account status changed to stopped
                    $account = Account::where('id', $this->argument('account_id'))->first();
                    if($account->status == 0)  die();

                    //Check platform for max buy price
                    if($account->platform == 'xbox')
                    {
                        $randomBid = rand(14000000, 15000000);
                        $formattedBid = floor($randomBid / 1000) * 1000;
                        if($item->type == 'consumable')
                        {
                            $ctype = "development";
                            $nationality = null;
                            $assetId = $item->asset_id;
                            $level = null;
                        }
                        else if($item->type == 'player')
                        {
                            $ctype = "player";
                            $nationality = null;
                            $assetId = $item->asset_id;
                            $level = null;
                        }
                        else if($item->type == 'nationality')
                        {
                            $ctype = "player";
                            $nationality = $item->asset_id;
                            $assetId = null;
                            if($item->rating == 0) $level = null;
                            if($item->rating == 1) $level = 'gold';
                            if($item->rating == 2) $level = 'silver';
                            if($item->rating == 3) $level = 'bronze';
                        }
                        try
                        {
                            $items_results = $fut->searchAuctions(
                                $ctype,
                                $level,
                                null,
                                $assetId,
                                null,
                                null,
                                $formattedBid,
                                null,
                                $item->xbox_buy_bin,
                                null,
                                null,
                                null,
                                null,
                                $nationality
                            );
                        }
                        catch(FutError $e)
                        {
                            $error = $e->GetOptions();
                            if($error['reason'] == 'permission_denied') die();
                            $account->phishingToken = null;
                            $account->sessionId = null;
                            $account->nucleusId = null;
                            $account->personaId = null;
                            $account->clubName = null;
                            $account->coins = null;
                            $account->dob = null;
                            $account->tradepile_limit = null;
                            $account->last_login = null;
                            $account->status = -1;
                            $account->status_reason = $error['reason'];
                            $account->save();
                            if($configuration->telegram_channel != "")
                            {
                                Telegram::sendMessage([
                                    'chat_id' => '@'.$configuration->telegram_channel, 
                                    'text' => 'We have this error on '. $account->email . ' account: ' . $error['reason']
                                ]);
                            }
                            $this->error($error['reason']);
                            $this->error("We have an error trying to search in market using following filters: AssetID->" .$item->asset_id ." , MaxBuy->". $item->xbox_buy_bin);
                            Log::error($error['reason']);
                            Log::error("We have an error trying to search in market using following filters: AssetID->" .$item->asset_id ." , MaxBuy->". $item->xbox_buy_bin);
                            die();
                        }
                    }

                    if($account->platform == 'ps')
                    {
                        $randomBid = rand(14000000, 15000000);
                        $formattedBid = floor($randomBid / 1000) * 1000;
                        if($item->type == 'consumable')
                        {
                            $ctype = "development";
                            $nationality = null;
                            $assetId = $item->asset_id;
                            $level = null;
                        }
                        else if($item->type == 'player')
                        {
                            $ctype = "player";
                            $nationality = null;
                            $assetId = $item->asset_id;
                            $level = null;
                        }
                        else if($item->type == 'nationality')
                        {
                            $ctype = "player";
                            $nationality = $item->asset_id;
                            $assetId = null;
                            if($item->rating == 0) $level = null;
                            if($item->rating == 1) $level = 'gold';
                            if($item->rating == 2) $level = 'silver';
                            if($item->rating == 3) $level = 'bronze';
                        }
                        try
                        {
                            $items_results = $fut->searchAuctions(
                                $ctype,
                                $level,
                                null,
                                $assetId,
                                null,
                                null,
                                $formattedBid,
                                null,
                                $item->ps_buy_bin,
                                null,
                                null,
                                null,
                                null,
                                $nationality
                            );
                        }
                        catch(FutError $e)
                        {
                            $error = $e->GetOptions();
                            if($error['reason'] == 'permission_denied') die();
                            $account->phishingToken = null;
                            $account->sessionId = null;
                            $account->nucleusId = null;
                            $account->personaId = null;
                            $account->clubName = null;
                            $account->coins = null;
                            $account->dob = null;
                            $account->tradepile_limit = null;
                            $account->last_login = null;
                            $account->status = -1;
                            $account->status_reason = $error['reason'];
                            $account->save();
                            if($configuration->telegram_channel != "")
                            {
                                Telegram::sendMessage([
                                    'chat_id' => '@'.$configuration->telegram_channel, 
                                    'text' => 'We have this error on '. $account->email . ' account: ' . $error['reason']
                                ]);
                            }
                            $this->error($error['reason']);
                            $this->error("We have an error trying to search in market using following filters: AssetID->" .$item->asset_id ." , MaxBuy->". $item->ps_buy_bin);
                            Log::error($error['reason']);
                            Log::error("We have an error trying to search in market using following filters: AssetID->" .$item->asset_id ." , MaxBuy->". $item->ps_buy_bin);
                            die();
                        }
                    }

                    if($account->platform == 'pc')
                    {
                        $randomBid = rand(14000000, 15000000);
                        $formattedBid = floor($randomBid / 1000) * 1000;
                        if($item->type == 'consumable')
                        {
                            $ctype = "development";
                            $nationality = null;
                            $assetId = $item->asset_id;
                            $level = null;
                        }
                        else if($item->type == 'player')
                        {
                            $ctype = "player";
                            $nationality = null;
                            $assetId = $item->asset_id;
                            $level = null;
                        }
                        else if($item->type == 'nationality')
                        {
                            $ctype = "player";
                            $nationality = $item->asset_id;
                            $assetId = null;
                            if($item->rating == 0) $level = null;
                            if($item->rating == 1) $level = 'gold';
                            if($item->rating == 2) $level = 'silver';
                            if($item->rating == 3) $level = 'bronze';
                        }
                        try
                        {
                            $items_results = $fut->searchAuctions(
                                $ctype,
                                $level,
                                null,
                                $assetId,
                                null,
                                null,
                                $formattedBid,
                                null,
                                $item->pc_buy_bin,
                                null,
                                null,
                                null,
                                null,
                                $nationality
                            );
                        }
                        catch(FutError $e)
                        {
                            $error = $e->GetOptions();
                            if($error['reason'] == 'permission_denied') die();
                            $account->phishingToken = null;
                            $account->sessionId = null;
                            $account->nucleusId = null;
                            $account->personaId = null;
                            $account->clubName = null;
                            $account->coins = null;
                            $account->dob = null;
                            $account->tradepile_limit = null;
                            $account->last_login = null;
                            $account->status = -1;
                            $account->status_reason = $error['reason'];
                            $account->save();
                            if($configuration->telegram_channel != "")
                            {
                                Telegram::sendMessage([
                                    'chat_id' => '@'.$configuration->telegram_channel, 
                                    'text' => 'We have this error on '. $account->email . ' account: ' . $error['reason']
                                ]);
                            }
                            $this->error($error['reason']);
                            $this->error("We have an error trying to search in market using following filters: AssetID->" .$item->asset_id ." , MaxBuy->". $item->pc_buy_bin);
                            Log::error($error['reason']);
                            Log::error("We have an error trying to search in market using following filters: AssetID->" .$item->asset_id ." , MaxBuy->". $item->pc_buy_bin);
                            die();
                        }
                    }
                    $items_results = json_decode(json_encode($items_results));
                    if(count($items_results->auctionInfo) > 0)
                    {
                        foreach($items_results->auctionInfo as $item_result)
                        {
                            try
                            {
                                if($account->coins >= $item_result->buyNowPrice)
                                {
                                    //Buy the player
                                    $fut->bid($item_result->tradeId, $item_result->buyNowPrice);
                                    $this->info("We bought ". $item->name . " for ". $item_result->buyNowPrice ." trying again in " . round(60/$configuration->rpm) . " seconds.");
                                    Log::info("We bought ". $item->name . " for ". $item_result->buyNowPrice ." trying again in " . round(60/$configuration->rpm) . " seconds.");
                                    
                                     //Check if player card is on the database
                                    $fifacard = FifaCard::where('definition_id', $item_result->itemData->resourceId)->first();
                                    //If not add it
                                    if(empty($fifacard))
                                    {
                                        $queryCard = json_decode(file_get_contents('https://www.easports.com/fifa/ultimate-team/api/fut/item?jsonParamObject&id=' . $item_result->itemData->resourceId));
                                        if($queryCard->items[0]->commonName != "")
                                        {
                                            $name = $queryCard->items[0]->commonName;
                                        }
                                        else
                                        {
                                            $name = $queryCard->items[0]->lastName;
                                        }
                                        $fifacard = new FifaCard();
                                        $fifacard->rating = $item_result->itemData->rating;
                                        $fifacard->type = Helpers::getCardType($item_result->itemData->rating, $item_result->itemData->rareflag);
                                        $fifacard->name = $name;
                                        $fifacard->position = $item_result->itemData->preferredPosition;
                                        $fifacard->club = $item_result->itemData->teamid;
                                        $fifacard->nationality = $item_result->itemData->nation;
                                        $fifacard->asset_id = $item_result->itemData->assetId;
                                        $fifacard->definition_id = $item_result->itemData->resourceId;
                                        $fifacard->save();
                                    }

                                    //Save the transaction in database and update account coins
                                    $transaction = new Transaction();
                                    $transaction->definition_id = $item_result->itemData->resourceId;
                                    $transaction->coins = $item_result->buyNowPrice;
                                    $transaction->type = "Buy";
                                    $transaction->account_id = $account->id;
                                    $transaction->save();

                                    //Update today's stats
                                    $stats->coins_balance -= $item_result->buyNowPrice;
                                    $stats->total_transactions += 1;
                                    $stats->save();

                                    $account->coins -= $item_result->buyNowPrice;
                                    $account->save();

                                    //Sell the player
                                    if($account->platform == 'xbox')
                                    {
                                        try
                                        {
                                            $fut->sendToTradepile($item_result->itemData->id, $safe = false);

                                            //Check Trade pile
                                            $tradepile = $fut->tradepile();
                                            $tradepile = json_decode(json_encode($tradepile));

                                            foreach($tradepile->auctionInfo as $itemTr)
                                            {
                                                if($itemTr->itemData->id == $item_result->itemData->id)
                                                {
                                                    $fut->sell($itemTr->itemData->id, $item->xbox_sell_bin - 100, $item->xbox_sell_bin);
                                                    $this->info('We put ' . $item->name . ' on sale for ' . $item->xbox_sell_bin);
                                                    Log::info('We put ' . $item->name . ' on sale for ' . $item->xbox_sell_bin);
                                                }
                                            }
                                        }
                                        catch(FutError $e)
                                        {
                                            $error = $e->GetOptions();
                                            $this->error($error['reason']);
                                            $this->error("We have an error trying to sell in market");
                                            Log::error($error['reason']);
                                            Log::error("We have an error trying to sell in market");
                                            if($configuration->telegram_channel != "")
                                            {
                                                Telegram::sendMessage([
                                                    'chat_id' => '@'.$configuration->telegram_channel, 
                                                    'text' => 'We have this error on '. $account->email . ' account: ' . $error['reason']
                                                ]);
                                            }
                                            die();
                                        }
                                    }
                                    else if($account->platform == 'ps')
                                    {
                                        try
                                        {
                                            $fut->sendToTradepile($item_result->itemData->id, $safe = false);

                                            //Check Trade pile
                                            $tradepile = $fut->tradepile();
                                            $tradepile = json_decode(json_encode($tradepile));

                                            foreach($tradepile->auctionInfo as $itemTr)
                                            {
                                                if($itemTr->itemData->id == $item_result->itemData->id)
                                                {
                                                    $fut->sell($itemTr->itemData->id, $item->ps_sell_bin - 100, $item->ps_sell_bin);
                                                    $this->info('We put ' . $item->name . ' on sale for ' . $item->ps_sell_bin);
                                                    Log::info('We put ' . $item->name . ' on sale for ' . $item->ps_sell_bin);
                                                }
                                            }
                                        }
                                        catch(FutError $e)
                                        {
                                            $error = $e->GetOptions();
                                            $this->error($error['reason']);
                                            $this->error("We have an error trying to sell in market");
                                            Log::error($error['reason']);
                                            Log::error("We have an error trying to sell in market");
                                            if($configuration->telegram_channel != "")
                                            {
                                                Telegram::sendMessage([
                                                    'chat_id' => '@'.$configuration->telegram_channel, 
                                                    'text' => 'We have this error on '. $account->email . ' account: ' . $error['reason']
                                                ]);
                                            }
                                            die();
                                        }
                                    }
                                    else if($account->platform == 'pc')
                                    {
                                        try
                                        {
                                            $fut->sendToTradepile($item_result->itemData->id, $safe = false);

                                            //Check Trade pile
                                            $tradepile = $fut->tradepile();
                                            $tradepile = json_decode(json_encode($tradepile));

                                            foreach($tradepile->auctionInfo as $itemTr)
                                            {
                                                if($itemTr->itemData->id == $item_result->itemData->id)
                                                {
                                                    $fut->sell($itemTr->itemData->id, $item->pc_sell_bin - 100, $item->pc_sell_bin);
                                                    $this->info('We put ' . $item->name . ' on sale for ' . $item->pc_sell_bin);
                                                    Log::info('We put ' . $item->name . ' on sale for ' . $item->pc_sell_bin);
                                                }
                                            }
                                        }
                                        catch(FutError $e)
                                        {
                                            $error = $e->GetOptions();
                                            $this->error($error['reason']);
                                            $this->error("We have an error trying to sell in market");
                                            Log::error($error['reason']);
                                            Log::error("We have an error trying to sell in market");
                                            if($configuration->telegram_channel != "")
                                            {
                                                Telegram::sendMessage([
                                                    'chat_id' => '@'.$configuration->telegram_channel, 
                                                    'text' => 'We have this error on '. $account->email . ' account: ' . $error['reason']
                                                ]);
                                            }
                                            die();
                                        }
                                    }
                                }
                                else
                                {
                                    $this->error("Not enought coins.");
                                    Log::error("Not enought coins.");
                                }
                            }
                            catch(FutError $e)
                            {
                                $error = $e->GetOptions();
                                if($error['reason'] == 'permission_denied') die();
                                $account->phishingToken = null;
                                $account->sessionId = null;
                                $account->nucleusId = null;
                                $account->personaId = null;
                                $account->clubName = null;
                                $account->coins = null;
                                $account->dob = null;
                                $account->tradepile_limit = null;
                                $account->last_login = null;
                                $account->status = -1;
                                $account->status_reason = $error['reason'];
                                $account->save();
                                if($configuration->telegram_channel != "")
                                {
                                    Telegram::sendMessage([
                                        'chat_id' => '@'.$configuration->telegram_channel, 
                                        'text' => 'We have this error on '. $account->email . ' account: ' . $error['reason']
                                    ]);
                                }
                                $this->error("We have an error trying to bid the selected player" . $error['reason']);
                                Log::error("We have an error trying to bid the selected player" . $error['reason']);
                                die();
                            }

                            $requestsDone++;
                            sleep(round(60/$configuration->rpm));
                        }
                    }
                    else
                    {
                        $requestsDone++;
                        sleep(round(60/$configuration->rpm));
                        $this->info("No results in market trying again in " . round(60/$configuration->rpm) . " seconds.");
                        Log::info("No results in market trying again in " . round(60/$configuration->rpm) . " seconds.");
                    }
                }
            }
        }
        $account->save();
    }
}