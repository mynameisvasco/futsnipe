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
use App\Club;
use App\League;

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

    public function clubs()
    {
        $clubs = Club::all();

        return $clubs;
    }

    public function leagues()
    {
        $leagues = League::all();

        return $leagues;
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
            $item->asset_id = $itemJson->iconId;
            $item->definition_id = $itemJson->resourceId;
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

            //If the consumable card is not in database generate it
            if(!isset($item->fifaCard))
            {
                $fifaCard = new FifaCard();
                $fifaCard->rating = 0;
                $fifaCard->type = $itemJson->type;
                $fifaCard->name = $itemJson->name;
                $fifaCard->position = "";
                $fifaCard->club = 0;
                $fifaCard->nationality = 0;
                $fifaCard->asset_id = $itemJson->iconId;
                $fifaCard->definition_id = $itemJson->resourceId;
                $fifaCard->save();
            }

            $item->save();

            return redirect('/items')->with('notify', array('message' => 'You added an item with success', 'icon' => 'icon-check', 'type' => 'success'));
        }
        else
        {
            //Check if it is a nationality
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

                //Generate the nationality card
                $fifacard = FifaCard::where('name', $itemJson->nationality)->first();
                if(empty($fifacard))
                {
                    $fifacard = new FifaCard();
                    $fifacard->rating = 0;
                    $fifacard->type = $request->input('nationalityQuality');
                    $fifacard->name = $itemJson->nationality;
                    $fifacard->position = $request->input('nationalityPosition');
                    $fifacard->club = 0;
                    $fifacard->nationality = $itemJson->nationality_id;
                    $fifacard->asset_id = 0;
                    $fifacard->definition_id = $itemJson->nationality_id * -1;
                    $fifacard->save();
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
                $item->position = $request->input('nationalityPosition');
                $item->definition_id = -1 * $itemJson->nationality_id; //If it is a nationality def id represents the flag id but with less sinal before the id
                $item->save();
            }
            //Check if it is a club
            else if(isset($itemJson->club_id))
            {
                //Case it is a club rating represents the card quality (GOLD, SILVER, BRONZE or ANY)
                switch($request->input('clubQuality'))
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

                //Generate the club card
                $fifacard = FifaCard::where('name', $itemJson->club)->first();
                if(empty($fifacard))
                {
                    $fifacard = new FifaCard();
                    $fifacard->rating = 0;
                    $fifacard->type = $request->input('clubQuality');
                    $fifacard->name = $itemJson->club;
                    $fifacard->position = $request->input('clubPosition');
                    $fifacard->position = "";
                    $fifacard->club = $itemJson->club_id;
                    $fifacard->nationality = 0;
                    $fifacard->asset_id = 0;
                    $fifacard->definition_id = 'C'.$itemJson->club_id;
                    $fifacard->save();
                }

                $item = new Item();
                $item->asset_id = $itemJson->club_id;
                $item->type = 'club';
                $item->rating = $rating;
                $item->xbox_buy_bin = 200;
                $item->ps_buy_bin = 200;
                $item->pc_buy_bin = 200;
                $item->xbox_sell_bin = 400;
                $item->ps_sell_bin = 400;
                $item->pc_sell_bin = 400;
                $item->user_id = Auth::user()->id;
                $item->name = $itemJson->club;
                $item->position = $request->input('clubPosition');
                $item->definition_id = 'C'.$itemJson->club_id;
                $item->save();
            }
            else
            {
                $prices = Helpers::getPrices($itemJson->def_id);
                $pricesXBOX = json_decode(json_encode(Helpers::calculatePrices($prices[0], $configuration->buy_percentage, $configuration->sell_percentage)));
                $pricesPS = json_decode(json_encode(Helpers::calculatePrices($prices[1], $configuration->buy_percentage, $configuration->sell_percentage)));
                $pricesPC = json_decode(json_encode(Helpers::calculatePrices($prices[2], $configuration->buy_percentage, $configuration->sell_percentage)));
            
                $item = new Item();
                $item->asset_id = $itemJson->player_id;
                $item->type = 'player';
                $item->rating = $itemJson->rating;
                $item->xbox_buy_bin = $pricesXBOX->max_bin;
                $item->ps_buy_bin = $pricesPS->max_bin;
                $item->pc_buy_bin = $pricesPC->max_bin;
                $item->xbox_sell_bin = $pricesXBOX->sell_bin;
                $item->ps_sell_bin = $pricesPS->sell_bin;
                $item->pc_sell_bin = $pricesPC->sell_bin;
                $item->user_id = Auth::user()->id;
                $item->definition_id = $itemJson->def_id;
                $item->name = $itemJson->card_name;
                $item->save();
            }
       
            return redirect('/items')->with('notify', array('message' => 'You added an item with success', 'icon' => 'icon-check', 'type' => 'success'));
        }
    }
 
    public function players()
    {
        return file_get_contents(env('EA_PLAYERS'));
    }

    public function playerCards($name)
    {
        return file_get_contents(env('FUTHEAD_PLAYERS'). $name);
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
        $isRare = $request->input('isRare');
        $assetId = $request->input('assetId');
        $rarityId = $request->input('rarityId');
        
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
        $fifacard->type = Helpers::getCardType($rating, $isRare, $rarityId);
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