<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Item;
use App\Helpers;
use App\Configuration;
use Image;
use Auth;
use App\Nationality;
use App\Consumable;
use App\FifaCard;

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
        $items = Item::with('fifaCard')->get();
        return view('items')->with('pageName', $pageName)
            ->with('items', $items);
    }
 
    public function update(Request $request)
    {
        $item = Item::find($request->input('id'));
        $item->xbox_buy_bin = $request->input('xbox_buy_bin');
        $item->ps_buy_bin = $request->input('ps_buy_bin');
        $item->pc_buy_bin = $request->input('pc_buy_bin');
        $item->xbox_sell_bin = $request->input('xbox_sell_bin');
        $item->ps_sell_bin = $request->input('ps_sell_bin');
        $item->pc_sell_bin = $request->input('pc_sell_bin');
        $item->save();

        return redirect('/items')->with('notify', array('message' => 'You updated an item with success', 'icon' => 'icon-check', 'type' => 'success'));
    }
    public function delete($id)
    {
        $item = Item::find($id);
        $item->delete();
 
        return redirect('/items')->with('notify', array('message' => 'You removed an item with success', 'icon' => 'icon-check', 'type' => 'success'));
    }
 
    public function nationalities()
    {
        $nationalities = Nationality::all();

        return $nationalities;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'item' => 'required',
        ]);
       
        $configuration = Configuration::where('user_id', Auth::user()->id)->first();
        $itemJson = json_decode($request->input('item'));
       
        //Check if item is a player (firstname) or a consumable 
        if(isset($itemJson->isConsumable))
        {
            $item = new Item();
            $item->asset_id = $itemJson->resourceId;
            $item->type = 'consumable';
            $item->rating = 0;
            $item->xbox_buy_bin = 200;
            $item->ps_buy_bin = 200;
            $item->pc_buy_bin = 200;
            $item->xbox_sell_bin = 400;
            $item->ps_sell_bin = 400;
            $item->pc_sell_bin = 400;
            $item->user_id = Auth::user()->id;
            $item->name = $itemJson->name;
            $item->save();

            return redirect('/items')->with('notify', array('message' => 'You added an item with success', 'icon' => 'icon-check', 'type' => 'success'));
        }
        else
        {
            //Check if it is a player or a nationality
            if(isset($itemJson->nationality_id))
            {
                //Case it is a nationality rating represents the card quality (GOLD, SILVER, BRONZE or ANY)
                switch($request->input('nationalityQuality'))
                {
                    case 'any':
                        $rating = 0;
                        break;
                    case 'gold':
                        $rating = 1;
                        break;
                    case 'silver':
                        $rating = 2;
                        break;
                    case 'bronze':
                        $rating = 3;
                        break;
                }
                $item = new Item();
                $item->asset_id = $itemJson->nationality_id;
                $item->type = 'nationality';
                $item->rating = $rating;
                $item->xbox_buy_bin = 200;
                $item->ps_buy_bin = 200;
                $item->pc_buy_bin = 200;
                $item->xbox_sell_bin = 400;
                $item->ps_sell_bin = 400;
                $item->pc_sell_bin = 400;
                $item->user_id = Auth::user()->id;
                $item->name = $itemJson->nationality;
                $item->save();
            }
            else
            {
                $prices = Helpers::getPrices($itemJson->id);
                $pricesXBOX = json_decode(json_encode(Helpers::calculatePrices($prices[0], $configuration->buy_percentage, $configuration->sell_percentage)));
                $pricesPS = json_decode(json_encode(Helpers::calculatePrices($prices[1], $configuration->buy_percentage, $configuration->sell_percentage)));
                $pricesPC = json_decode(json_encode(Helpers::calculatePrices($prices[2], $configuration->buy_percentage, $configuration->sell_percentage)));
            
                $item = new Item();
                $item->asset_id = $itemJson->baseId;
                $item->type = 'player';
                $item->rating = $itemJson->rating;
                $item->xbox_buy_bin = $pricesXBOX->max_bin;
                $item->ps_buy_bin = $pricesPS->max_bin;
                $item->pc_buy_bin = $pricesPC->max_bin;
                $item->xbox_sell_bin = $pricesXBOX->sell_bin;
                $item->ps_sell_bin = $pricesPS->sell_bin;
                $item->pc_sell_bin = $pricesPC->sell_bin;
                $item->user_id = Auth::user()->id;
                $item->definition_id = $itemJson->id;
                if($itemJson->commonName != "")
                {
                    $item->name = $itemJson->commonName;
                }
                else
                {
                    $item->name = $itemJson->lastName;
                }
                $item->save();
            }
       
            return redirect('/items')->with('notify', array('message' => 'You added an item with success', 'icon' => 'icon-check', 'type' => 'success'));
        }
    }
 
    public function players()
    {
        return file_get_contents(env('EA_PLAYERS'));
    }

    public function playerCards($assetId)
    {
        return file_get_contents(env('EA_PLAYER_CARDS').'&baseid=' . $assetId);
    }
 
    public function consumables()
    {
        //Just fitness and expensive chemistry styles
        $consumables = Consumable::all();
        
        return $consumables;

    }

    public function generateCard(Request $request)
    {
        $definitionId = $request->input('definitionId');
        $fifacard = FifaCard::where('definition_id', $definitionId)->first();

        //If card is already on database
        if(isset($fifacard))
        {
            return 'card already generated';
        }

        $name = $request->input('name');
        $rating = $request->input('rating');
        $club = $request->input('club');
        $nationality = $request->input('nationality');
        $position = $request->input('position');
        $rarityId = $request->input('rarityId');
        $assetId = $request->input('assetId');

        $rarityIds = array(
            '70'=> 'champions-teamoftournment',
            '72'=> 'carnibal',
            '12'=> 'icon',
            '71'=> 'futurestars',
            '51'=> 'flashback',
            '16'=> 'futties',
            '85'=> 'headliners',
            '4'=> 'hero',
            '5'=> 'toty',
            '30'=> 'specialitem',
            '28'=> 'award-item',
            '66'=> 'tots',
            '18'=> 'futchampions',
            '1'=> 'rare',
            '0'=> 'non-rare',
            '8'=> 'orange',
            '48'=> 'champions-rare',
            '49'=> 'champions-manofmatch',
            '50'=> 'champions-live',
            '69'=> 'champions-sbc',
            '3'=> 'goldif',
            '46'=> 'europaleague-live',
            '68'=> 'europaleague-teamoftournment',
            '45'=> 'europaleague-manofmatch',
            '43'=> 'premierleague-playerofmonth',
            '32'=> 'futmas',
            '63'=> 'sbcsummer'
        );

        $fifacard = new FifaCard();
        if($rarityId == 1)
        {
            if($rating >= 75) $fifacard->type = 'goldrare';
            if($rating >= 65 && $rating <= 74) $fifacard->type = 'silverrare';
            if($rating >= 0 && $rating <= 64) $fifacard->type = 'bronzerare';
        }
        else if($rarityId == 0)
        {
            if($rating >= 75) $fifacard->type = 'gold';
            if($rating >= 65 && $rating <= 74) $fifacard->type = 'silver';
            if($rating >= 0 && $rating <= 64) $fifacard->type = 'bronze';
        }
        else
        {
            $fifacard->type = $rarityIds[$rarityId];
        }
        $fifacard->rating = $rating;
        $fifacard->name = $name;
        $fifacard->position = $position;
        $fifacard->club = $club;
        $fifacard->nationality = $nationality;
        $fifacard->asset_id = $assetId;
        $fifacard->definition_id = $definitionId;
        $fifacard->save();

        return 'card generated with success';
    }
}