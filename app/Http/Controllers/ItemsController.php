<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Item;
use App\Helpers;
use App\Configuration;
use Image;
use Auth;
use App\Nationality;
 
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
 
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'item' => 'required',
        ]);
       
        $configuration = Configuration::where('user_id', Auth::user()->id)->first();
        $itemJson = json_decode($request->input('item'));
       
        //Check if item is a player (firstname), a league (league_id) or a nationality (nationality_id)
        if(isset($itemJson->nationality_id))
        {
            //...
        }
        else if(isset($itemJson->league_id))
        {
            //...
        }
        else
        {
            $prices = Helpers::getPrices($itemJson->id);
            $pricesXBOX = json_decode(json_encode(Helpers::calculatePrices($prices[0], $configuration->buy_percentage, $configuration->sell_percentage)));
            $pricesPS = json_decode(json_encode(Helpers::calculatePrices($prices[1], $configuration->buy_percentage, $configuration->sell_percentage)));
            $pricesPC = json_decode(json_encode(Helpers::calculatePrices($prices[2], $configuration->buy_percentage, $configuration->sell_percentage)));
           
            $item = new Item();
            $item->asset_id = $itemJson->id;
            $item->type = 'player';
            $item->rating = $itemJson->rating;
            $item->xbox_buy_bin = $pricesXBOX->max_bin;
            $item->ps_buy_bin = $pricesPS->max_bin;
            $item->pc_buy_bin = $pricesPC->max_bin;
            $item->xbox_sell_bin = $pricesXBOX->sell_bin;
            $item->ps_sell_bin = $pricesPS->sell_bin;
            $item->pc_sell_bin = $pricesPC->sell_bin;
            $item->user_id = Auth::user()->id;
           
            if($itemJson->commonName != "")
            {
                $item->name = $itemJson->commonName;
            }
            else
            {
                $item->name = $itemJson->lastName;
            }
            $item->save();
       
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
 
    public function nationalities()
    {
        $nationalities = Nationality::all();
 
        return $nationalities;
    }

    public function generateCard(Request $request)
    {
        $name = $request->input('name');
        $rating = $request->input('rating');
        $club = $request->input('club');
        $assetId = $request->input('assetId');
        $nationality = $request->input('nationality');
        $position = $request->input('position');
        $rarityId = $request->input('rarityId');
        $definitionId = $request->input('definitionId');

        if(file_exists(storage_path('app/public/fut_cards/'. $definitionId .'.png'))) return 'card already generated';

        $cardsBg = array(
            //CHAMPIONS LEAGUE TEAM OF THE TOURNMENT
            '70'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_70_0.png', '#f5f5f5'],
            //CANIBAL
            '72'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_72_0.png', '#ffe632' ],
            //ICON
            '12'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_12_0.png', '#625217'],
            //FUTURE STARS
            '71'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_71_0.png', '#c0ff36'],
            //FLASHBACK
            '51'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_51_0.png', '#e4d7bc'],
            //SPECIAL ITEM PINK
            '16'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_16_0.png', '#f9e574'],
            //HEADLINERS
            '85'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_85_0.png', '#ffffff'],
            //HERO
            '4'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_4_0.png', '#cbb8f9'],
            //TOTY
            '5'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_5_0.png', '#ebcd5b'],
            //SPECIAL ITEM
            '30'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_30_0.png', '#12fcc6'],
            //AWARD WINNER
            '28'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_28_0.png', '#c0ff36'],
            //TOTS
            '66'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_66_0.png', '#eed170'],
            //FUTCHAMPIONS
            '18'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_18_3.png', '#e3cf83'],
            //RARE
            '1'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_1_3.png', '#46390c'],
            //NON-RARE
            '0'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_0_3.png', '#26292a'],
            //DOMESTIC MAN OF THE MATCH (ORANGE CARD)
            '8'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_8_0.png', '#f5f5f5'],
            //CHAMPIONS LEAGUE RARE
            '48'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_48_0.png', '#f5f5f5'],
            //CHAMPIONS MOTM
            '49'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_49_0.png', '#f5f5f5'],
            //CHAMPIONS LEAGUE LIVE
            '50'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_50_0.png', '#f5f5f5'],
            //CHAMPIONS LEAGUE PREMIUM SBC
            '69'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_69_0.png', '#f5f5f5'],
            //INFORM GOLD
            '3'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_3_3.png', '#e9cc74'],   
            //EUROPA LEAGUE LIVE
            '46'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_46_0.png', '#f39200'],
            //PREMIER LEAGUE POTM
            '43'=> ['https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_43_0.png', '#05f1ff'],
        );

        $clubImg = env('EA_CLUB_BADGE') . $club . ".png"; 
        $nationalityImg = env('EA_NATION_FLAGS') . $nationality . ".png"; 
        $playerFaceImg = env('EA_PLAYERS_PIC') . $assetId . ".png";

        if($rating >= 75)
            $cardImg = Image::make(file_get_contents($cardsBg[$rarityId][0]));  
        elseif($rating > 64 && $rating <= 74)
            if($rarityId == 1)
                $cardImg = Image::make(file_get_contents('https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_1_2.png'));
            else
                $cardImg = Image::make(file_get_contents('https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_0_2.png'));
        elseif($rating >= 0 && $rating <= 64)
            if($rarityId == 1)
                $cardImg = Image::make(file_get_contents('https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_1_1.png'));
            else
                $cardImg = Image::make(file_get_contents('https://www.easports.com/fifa/ultimate-team/web-app/content/7D49A6B1-760B-4491-B10C-167FBC81D58A/2019/fut/items/images/backgrounds/itemCompanionBGs/large/cards_bg_e_1_0_1.png'));

        $playerFaceImg = Image::make($playerFaceImg)->resize(null, 300, function ($constraint) {
            $constraint->aspectRatio();
        });

        $clubImg = Image::make($clubImg)->resize(null, 70, function ($constraint) {
            $constraint->aspectRatio();
        });

        // use callback to define details
        $cardImg->text(strtoupper($name), 268, 450, function($font) use ($cardsBg,$rarityId) {
            $font->file(storage_path('app/DINCondensed-Bold.ttf'));
            $font->size(55);
            $font->color($cardsBg[$rarityId][1]);
            $font->align('center');
            $font->valign('top');
        });

        // use callback to define details
        $cardImg->text(strtoupper($rating), 105, 120, function($font) use ($cardsBg,$rarityId) {
            $font->file(storage_path('app/DINCondensed-Bold.ttf'));
            $font->size(80);
            $font->color($cardsBg[$rarityId][1]);
            $font->align('center');
            $font->valign('top');
        });

        // use callback to define details
        $cardImg->text(strtoupper($position), 105, 200, function($font) use ($cardsBg,$rarityId) {
            $font->file(storage_path('app/DINCondensed-Bold.ttf'));
            $font->size(40);
            $font->color($cardsBg[$rarityId][1]);
            $font->align('center');
            $font->valign('top');
        });


        $cardImg->insert($playerFaceImg, '', 200, 137);
        $cardImg->insert($nationalityImg, '', 70, 270);
        $cardImg->insert($clubImg, '', 70, 330);
        $cardImg->save(storage_path('app/public/fut_cards/'. $definitionId .'.png'));
    }
}