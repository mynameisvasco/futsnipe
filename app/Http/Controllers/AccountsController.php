<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use Auth;
use Artisan;

class AccountsController extends Controller
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

    public function refresh($id)
    {
        Artisan::call('accounts:cron ' . $id);
        return redirect('/accounts')->with('notify', array('message' => 'We are trying to login into your account', 'icon' => 'icon-check', 'type' => 'success'));
    }

    public function stop($id)
    {
        $account = Account::find($id);
        $account->status = 0;
        $account->save();

        return redirect('/accounts')->with('notify', array('message' => 'We just stopped the account from snipping', 'icon' => 'icon-check', 'type' => 'success'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'platform' => 'required'
        ]);

        $account = new Account();
        $account->email = $request->input('email');
        $account->password = $request->input('password');
        $account->platform = $request->input('platform');
        $account->backupCodes = $request->input('backup_codes');
        $account->user_id = Auth::user()->id;
        $account->save();

        return redirect('/accounts')->with('notify', array('message' => 'You added an account with success', 'icon' => 'icon-check', 'type' => 'success'));
    }

    public function index()
    {
        $pageName = 'Accounts';

        $accounts = Account::where('user_id', Auth::user()->id)->get();

        return view('accounts')->with('accounts', $accounts)
            ->with('pageName', $pageName);
    }
}
