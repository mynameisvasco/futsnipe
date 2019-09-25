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

        $transactions = Transaction::with('fifaCard')->where('user_id', auth()->user()->id)->orderBy('created_at', 'DESC')->paginate(12);

        return view('transactions')->with('pageName', $pageName)
            ->with(['transactions' => $transactions]);
    }

    public function indexDate(Request $request)
    {
        $pageName = "Transactions";

        $transactions = Transaction::whereDate('created_at', '=' , date($request->input('date')))->where('user_id', auth()->user()->id)->paginate(12);

        return view('transactions')->with('pageName', $pageName)
            ->with(['transactions' => $transactions]);
    }
}
