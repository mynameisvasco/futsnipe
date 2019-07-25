<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Account;
use FUTApi\Core;
use FUTApi\FutError;
use Carbon\Carbon;
use Log;

class AccountsLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:cron {account_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Login in all available accounts and save information to database';

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
        $account = Account::find($this->argument('account_id'));

        //If there is no account with id provided
        if(empty($account))
        {
            $this->error("There is no account with ID->  ".$this->argument('account_id'));
            Log::error("There is no account with ID->  ".$this->argument('account_id'));
            die(); 
        }

        //If account is being used don't refresh
        if($account->status == 2) die();

        $backup_codes = explode(',', trim($account->backupCodes));
        try 
        {
            $fut = new Core(
                $account->email,
                $account->password,
                strtolower($account->platform),
                $backup_codes[array_rand($backup_codes, 1)],
                false,
                false,
                storage_path(
                    'app/fut_cookies/'.md5($account->email)
                )
            );
            $login = $fut->login();
            $account->phishingToken = $login['auth']['phishing_token'];
            $account->personaId = $login['mass_info']['userInfo']['personaId'];
            $account->personaName = $login['mass_info']['userInfo']['personaName'];
            $account->nucleusId = $login['auth']['nucleus_id'];
            $account->clubName = $login['mass_info']['userInfo']['clubName'];
            $account->sessionId = $login['auth']['session_id'];
            $account->coins = $login['mass_info']['userInfo']['credits'];
            $account->tradepile_limit = $login['mass_info']['pileSizeClientData']['entries'][0]['value'];
            $account->dob = $login['auth']['dob'];
            $account->last_login = new Carbon;
            $account->status = 2;
            $account->status_reason = null;
            $account->save();
            $this->info("We updated ".$account->email." successfully!");
            Log::info("We updated ".$account->email." successfully!");
        }
        catch(FutError $exception) 
        {
            $error = $exception->GetOptions();
            $account->status = -1;
            $account->status_reason = $error['reason'];
            $account->last_login = new Carbon;
            $account->save();
            $this->error("Error ".$error['reason']." on account: ".$account->email);
            Log::error("Error ".$error['reason']." on account: ".$account->email);
        }
    }
}
