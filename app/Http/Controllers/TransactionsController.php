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

        $transactions = Transaction::with('fifaCard')->orderBy('created_at', 'DESC')->paginate(12);

        return view('transactions')->with('pageName', $pageName)
            ->with(['transactions' => $transactions]);
    }

    public function indexDate(Request $request)
    {
        $pageName = "Transactions";

        $transactions = Transaction::whereDate('created_at', '=' , date($request->input('date')))->paginate(12);

        return view('transactions')->with('pageName', $pageName)
            ->with(['transactions' => $transactions]);
    }
}
