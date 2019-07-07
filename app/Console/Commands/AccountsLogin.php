<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Account;
use FUTApi\Core;
use FUTApi\FutError;
use Carbon\Carbon;

class AccountsLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:cron';

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
        //For each user in database get their fifa accounts
        $accounts = Account::where('status', '<>', '0')->whereNull('phishingToken')->orderByRaw("RAND()")->get();
        foreach($accounts as $account)
        {
            $account = Account::find($account->id);
            $backup_codes = explode(',', trim($account->backupCodes));
            if($account->status != 0)
            {
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
                }
                catch(FutError $exception) 
                {
                    $error = $exception->GetOptions();
                    $account->status = '-1';
                    $account->status_reason = $error['reason'];
                    $account->last_login = new Carbon;
                    $account->save();
                    $this->info("Error ".$error['reason']." on account: ".$account->email);
                }
            }
        }
    }
}
