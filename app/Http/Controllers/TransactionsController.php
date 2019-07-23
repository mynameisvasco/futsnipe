<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;

class TransactionsController extends Controller
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

    public function index()
    {
        $pageName = "Transactions";
        $transactions = Transaction::all();

        return view('transactions')->with('pageName', $pageName)
            ->with('transactions', $transactions);
    }
}
