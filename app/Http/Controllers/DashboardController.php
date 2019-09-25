<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\Item;
use Auth;
use App\Account;
use App\Stats;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $pageName = "Dashboard";
        $transactions = Transaction::with('fifaCard')->where('user_id', auth()->user()->id)->orderBy('created_at', 'DESC')->limit(6)->get();
        $stats = Stats::latest()->limit(6)->where('user_id', auth()->user()->id)->get();
        $totalItems = count(Item::where('user_id', Auth::user()->id)->get());
        $totalAccounts = count(Account::where('user_id', Auth::user()->id)->get());
        $totalEarnings = 0;
        $totalTransactions = 0;
        foreach($stats as $stat)
        {
            $totalEarnings += $stat->coins_balance;
            $totalTransactions += $stat->total_transactions;
        }
        return view('dashboard')->with('pageName', $pageName)->with('totalItems', $totalItems)
            ->with('totalAccounts', $totalAccounts)
            ->with('transactions', $transactions)
            ->with('stats', $stats)
            ->with('totalEarnings', $totalEarnings)
            ->with('totalTransactions', $totalTransactions);
    }
}
