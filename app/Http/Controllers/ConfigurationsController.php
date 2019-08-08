<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuration;
use Auth;

class ConfigurationsController extends Controller
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

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'rpm' => 'required|numeric',
            'snipe_cooldown' => 'required|numeric',
            'price_update_cooldown' => 'required|numeric',
            'buy_percentage' => 'required|numeric',
            'sell_percentage' => 'required|numeric',
        ]);

        $configuration = Configuration::where('user_id', Auth::user()->id)->first();
        $configuration->rpm = $request->input('rpm');
        $configuration->snipe_cooldown = $request->input('snipe_cooldown');
        $configuration->price_update_cooldown = $request->input('price_update_cooldown');
        $configuration->buy_percentage = $request->input('buy_percentage');
        $configuration->sell_percentage = $request->input('sell_percentage');
        $configuration->telegram_channel = $request->input('telegram_channel');
        $configuration->save();

        return redirect('/configurations')->with('notify', array('message' => 'You updated your configuration with success', 'icon' => 'icon-check', 'type' => 'success'));


    }

    public function index()
    {
        //Detected if user has configuration already, if not creates it with default values
        $configuration = Configuration::where('user_id', Auth::user()->id)->first();
        if(isset($configuration) == 0)
        {
            //Creates a new configuration for current user
            $configuration = new Configuration();
            $configuration->rpm = 6;
            $configuration->snipe_cooldown = 60;
            $configuration->price_update_cooldown = 15;
            $configuration->buy_percentage = 90;
            $configuration->sell_percentage = 95;
            $configuration->user_id = Auth::user()->id;
            $configuration->save();
        }
        $pageName = 'Configurations';
        $configuration = Configuration::where('user_id', Auth::user()->id)->first();
        return view('configurations')->with('pageName', $pageName)
            ->with('configuration', $configuration);
    }
}
