<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Account;
use Artisan;
use Log;
use App\Transaction;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Snipe players using the accounts that have status = 2 (READY TO SNIPE) and status = 0 (COOLDOWN)
        $schedule->call(function () {
            $accounts = Account::where('status', 2)->orWhere('status', 3)->get();
            foreach($accounts as $account)
            {
                Artisan::call('snipeplayers:cron ' . $account->id);
            }
        })->everyMinute();

        $schedule->call(function () {
            $transactions = Transaction::all();
            if(count($transactions) > 300)
            {
                Transaction::latest()->skip(6)->delete();
                Log::info("We deleted old transactions details from database");
            }
        })->everyThirtyMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
