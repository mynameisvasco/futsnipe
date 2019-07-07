<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Helpers;
use App\Configuration;
use Auth;

class ItemsController extends Controller
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
        $pageName = 'Items';
        $items = Item::all();
        return view('items')->with('pageName', $pageName)
            ->with('items', $items);
    }

    public function delete($id)
    {
        $item = Item::find($id);
        $item->delete();

        return redirect('/items')->with('notify', array('message' => 'You removed an item with success', 'icon' => 'icon-check', 'type' => 'success'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'item' => 'required',
        ]);
        
        $configuration = Configuration::where('user_id', Auth::user()->id)->first();
        $itemJson = json_decode($request->input('item'));
        
        $prices = Helpers::getPrices($itemJson->id);
        $pricesXBOX = json_decode(json_encode(Helpers::calculatePrices($prices[0], $configuration->buy_percentage, $configuration->sell_percentage)));
        $pricesPS = json_decode(json_encode(Helpers::calculatePrices($prices[1], $configuration->buy_percentage, $configuration->sell_percentage)));
        $pricesPC = json_decode(json_encode(Helpers::calculatePrices($prices[2], $configuration->buy_percentage, $configuration->sell_percentage)));
        
        $item = new Item();
        $item->asset_id = $itemJson->id;
        $item->type = 'player';
        $item->rating = $itemJson->r;
        $item->xbox_buy_bin = $pricesXBOX->max_bin;
        $item->ps_buy_bin = $pricesPS->max_bin;
        $item->pc_buy_bin = $pricesPC->max_bin;
        $item->xbox_sell_bin = $pricesXBOX->sell_bin;
        $item->ps_sell_bin = $pricesPS->sell_bin;
        $item->pc_sell_bin = $pricesPC->sell_bin;
        $item->user_id = Auth::user()->id;
        
        if(isset($itemJson->c))
        {
            $item->name = $itemJson->c;
        }
        else
        {
            $item->name = $itemJson->f . " " . $itemJson->l;
        }
        $item->save();
    
        return redirect('/items')->with('notify', array('message' => 'You added an item with success', 'icon' => 'icon-check', 'type' => 'success'));
    }

    public function players()
    {
        return file_get_contents(env('EA_PLAYERS'));
    }
}
