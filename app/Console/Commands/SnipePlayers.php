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
    protected $signature = 'snipeplayers:cron';

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
        while(true)
        {
            //Get all users
            $users = User::all();
            //For each user in database get their fifa accounts
            foreach($users as $user)
            {
                $accounts = Account::where('status', 2)->where('user_id', $user->id)->get();
                $configuration = Configuration::where('user_id', $user->id)->first();

                //For each fifa account in database login
                foreach($accounts as $account)
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
                        die();
                    }
                    

                    $this->info("Logged in with success as " . $account->email);
                    Log::info("Logged in with success as " . $account->email);


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
                                die();
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
                                die();
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
                                        $fut->bid($item_result->tradeId, $item_result->buyNowPrice);
                                        $this->info("We bought ". $item->name . " for ". $item_result->buyNowPrice ." trying again in " . round(60/$configuration->rpm) . " minutes.");
                                        Log::info("We bought ". $item->name . " for ". $item_result->buyNowPrice ." trying again in " . round(60/$configuration->rpm) . " minutes.");
                                        $transaction = new Transaction();
                                        $transaction->asset_id = $item->asset_id;
                                        $transaction->name = $item->name;
                                        $transaction->coins = $item_result->buyNowPrice;
                                        $transaction->type = "Buy";
                                        $transaction->account_id = $account->id;
                                        $transaction->save();
                                        $account->coins -= $item_result->buyNowPrice;
                                        $account->save();
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
                                    die();
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
                        sleep($configuration->rpm);
                    }
                }
            }
        }

    }
}
