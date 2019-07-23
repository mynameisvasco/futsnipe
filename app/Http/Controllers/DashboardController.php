<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\Item;
use Auth;
use App\Account;

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
        $transactions = Transaction::orderBy('created_at', 'DESC')->get();
        $latestTransactions = [];
        $totalTransactions = 0;
        $totalEarnings = 0;
        $totalItems = count(Item::where('user_id', Auth::user()->id)->get());
        $totalAccounts = count(Account::where('user_id', Auth::user()->id)->get());
        foreach($transactions as $transaction)
        {
            if($transaction->type == 'Buy') $totalEarnings -= $transaction->coins;
            else $totalEarnings += $transaction->coins;
            if($transaction->account->user_id == Auth::user()->id) $totalTransactions++;
        }
        return view('dashboard')->with('pageName', $pageName)->with('totalTransactions', $totalTransactions)
            ->with('totalEarnings', $totalEarnings)
            ->with('totalItems', $totalItems)
            ->with('totalAccounts', $totalAccounts)
            ->with('transactions', $transactions);
    }
}
