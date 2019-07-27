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
use Log;
 
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
                $this->info($account->email . ' is now in cooldown for ' . $configuration->snipe_cooldown);
                Log::info($account->email . ' is now in cooldown for ' . $configuration->snipe_cooldown);
                die();
            }
            //If cooldown time passed start again
            elseif($account->minutesRunning == $configuration->snipe_cooldown * 2)
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

                //Set account status = 1 (Accounts in use)
                $account->status = 1;
                $account->save();
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

                $this->error("We have an error logging in: ".$error['reason']);
                Log::error("We have an error logging in: ".$error['reason']);
                
            }


            $this->info("Logged in with success as " . $account->email);
            Log::info("Logged in with success as " . $account->email);

            $startedTime = Carbon::now();

            //Check Trade pile
            $tradepile = $fut->tradepile();
            $tradepile = json_decode(json_encode($tradepile));
            foreach($tradepile->auctionInfo as $item)
            {
                $this->info(json_encode($tradepile));
                //If trade is closed mark as sold
                if($item->tradeState == 'closed')
                {
                    try
                    {
                        $fut->removeSold($item->tradeId);
                    }
                    catch(FutError $e)
                    {
                        $account->status -= 2;
                        $account->save();
                    }

                    //Save the transaction in database and update account coins
                    $transaction = new Transaction();
                    $transaction->asset_id = $item->itemData->assetId;
                    $transaction->name = "Needs to be fixed";
                    $transaction->coins = $item->buyNowPrice;
                    $transaction->type = "Sell";
                    $transaction->account_id = $account->id;
                    $transaction->save();
                    $account->coins += $item->buyNowPrice;
                    $account->save();
                }
            }
            
            $requestsDone = 0;

            while($requestsDone < $configuration->rpm)
            {

                if(Carbon::now()->diffInMinutes($startedTime) >= 1)
                {
                    $account->minutesRunning += 1;
                    $account->save();
                }

                //Get all user snipe items
                $items = Item::where('user_id', $user->id)->get();

                //For each item in database that belongs to current user
                foreach($items as $item)
                {
                    //Check platform for max buy price
                    if($account->platform == 'xbox')
                    {
                        $randomBid = rand(14000000, 15000000);
                        $formattedBid = floor($randomBid / 1000) * 1000;
                        try
                        {
                            $items_results = $fut->searchAuctions(
                                'player',
                                null,
                                null,
                                $item->asset_id,
                                null,
                                null,
                                $formattedBid,
                                null,
                                $item->xbox_buy_bin
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

                            $this->error($error['reason']);
                            $this->error("We have an error trying to search in market using following filters: AssetID->" .$item->asset_id ." , MaxBuy->". $item->xbox_buy_bin);
                            Log::error($error['reason']);
                            Log::error("We have an error trying to search in market using following filters: AssetID->" .$item->asset_id ." , MaxBuy->". $item->xbox_buy_bin);
                            
                        }
                    }

                    if($account->platform == 'ps')
                    {
                        $randomBid = rand(14000000, 15000000);
                        $formattedBid = floor($randomBid / 1000) * 1000;
                        try
                        {
                            $items_results = $fut->searchAuctions(
                                'player',
                                null,
                                null,
                                $item->asset_id,
                                null,
                                null,
                                $formattedBid,
                                null,
                                $item->ps_buy_bin
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
                            
                            $this->error($error['reason']);
                            $this->error("We have an error trying to search in market using following filters: AssetID->" .$item->asset_id ." , MaxBuy->". $item->ps_buy_bin);
                            Log::error($error['reason']);
                            Log::error("We have an error trying to search in market using following filters: AssetID->" .$item->asset_id ." , MaxBuy->". $item->ps_buy_bin);
                            
                        }
                    }

                    if($account->platform == 'pc')
                    {
                        $randomBid = rand(14000000, 15000000);
                        $formattedBid = floor($randomBid / 1000) * 1000;
                        try
                        {
                            $items_results = $fut->searchAuctions(
                                'player',
                                null,
                                null,
                                $item->asset_id,
                                null,
                                null,
                                $formattedBid,
                                null,
                                $item->pc_buy_bin
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

                            $this->error($error['reason']);
                            $this->error("We have an error trying to search in market using following filters: AssetID->" .$item->asset_id ." , MaxBuy->". $item->pc_buy_bin);
                            Log::error($error['reason']);
                            Log::error("We have an error trying to search in market using following filters: AssetID->" .$item->asset_id ." , MaxBuy->". $item->pc_buy_bin);
                            
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
                                    
                                    //Save the transaction in database and update account coins
                                    $transaction = new Transaction();
                                    $transaction->asset_id = $item->asset_id;
                                    $transaction->name = $item->name;
                                    $transaction->coins = $item_result->buyNowPrice;
                                    $transaction->type = "Buy";
                                    $transaction->account_id = $account->id;
                                    $transaction->save();
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
                                                    $fut->sell($itemTr->itemData->id, $item->pc_sell_bin - 500, $item->xbox_sell_bin);
                                                    $this->info('We put ' . $item->name . ' on sale for ' . $item->xbox_sell_bin);
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
                                                    $fut->sell($itemTr->itemData->id, $item->pc_sell_bin - 500, $item->ps_sell_bin);
                                                    $this->info('We put ' . $item->name . ' on sale for ' . $item->ps_sell_bin);
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
                                                    $fut->sell($itemTr->itemData->id, $item->pc_sell_bin - 500, $item->pc_sell_bin);
                                                    $this->info('We put ' . $item->name . ' on sale for ' . $item->pc_sell_bin);
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

                                $this->error("We have an error trying to bid the selected player" . $error['reason']);
                                Log::error("We have an error trying to bid the selected player" . $error['reason']);
                                
                            }
                        }
                    }
                    else
                    {
                        $this->info("No results in market trying again in " . $configuration->rpm . " seconds.");
                        Log::info("No results in market trying again in " . $configuration->rpm . " seconds.");
                    }
                    $account->status = 2;
                    $account->save();

                    $requestsDone++;
                    sleep(round(60/$configuration->rpm));
                }
            }
        }
        //This script run once per minute so we can increase counter at the end of the script
        $account->minutesRunning += 1;
        $account->save();
    }
}