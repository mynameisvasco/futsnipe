<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Helpers extends Model
{
    public const rarityIds = array(
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

    public static function getCardType($rating, $rarityId)
    {
        if($rarityId == 1)
        {
            if($rating >= 75) return 'goldrare';
            if($rating >= 65 && $rating <= 74) return 'silverrare';
            if($rating >= 0 && $rating <= 64) return 'bronzerare';
        }
        else if($rarityId == 0)
        {
            if($rating >= 75) return 'gold';
            if($rating >= 65 && $rating <= 74) return 'silver';
            if($rating >= 0 && $rating <= 64) return 'bronze';
        }
        else
        {
            return $this->rarityIds[$rarityId];
        }
    }
    public static function getPrices($playerId)
    {
        $futbinPrice = json_decode(file_get_contents(env('FUTBIN_PRICE') . $playerId));
        $xbox = $futbinPrice->$playerId->prices->xbox->LCPrice;
        $ps = $futbinPrice->$playerId->prices->ps->LCPrice;
        $pc = $futbinPrice->$playerId->prices->pc->LCPrice;

        $xbox2 = $futbinPrice->$playerId->prices->xbox->LCPrice2;
        $ps2 = $futbinPrice->$playerId->prices->ps->LCPrice2;
        $pc2 = $futbinPrice->$playerId->prices->pc->LCPrice2;

        $xbox3 = $futbinPrice->$playerId->prices->xbox->LCPrice3;
        $ps3 = $futbinPrice->$playerId->prices->ps->LCPrice3;
        $pc3 = $futbinPrice->$playerId->prices->pc->LCPrice3;

        $xbox = str_replace(",", "", $xbox);
        $ps = str_replace(",", "", $ps);
        $pc = str_replace(",", "", $pc);

        $xbox2 = str_replace(",", "", $xbox2);
        $ps2 = str_replace(",", "", $ps2);
        $pc2 = str_replace(",", "", $pc2);

        $xbox3 = str_replace(",", "", $xbox3);
        $ps3 = str_replace(",", "", $ps3);
        $pc3 = str_replace(",", "", $pc3);
        
        $xbox = $xbox + $xbox2 + $xbox3;
        $ps = $ps + $ps2 + $ps3;
        $pc = $pc + $pc2 + $pc3;

        $xbox = round($xbox/3);
        $ps = round($ps/3);
        $pc = round($pc/3);

        

        return [$xbox, $ps, $pc];
    }

    public static function calculatePrices($lowest_bin, $buy_percentage, $sell_percentage)
    {
        if($lowest_bin < 10000) {
            $sell_bin = $sell_percentage / 100 * $lowest_bin;
            $sell_bin = ceil($sell_bin / 100) * 100;
            $sell_bid = $sell_bin - 100;
            $new_bin = $buy_percentage / 100 * $lowest_bin;
            $buy_bin = floor($new_bin / 100) * 100;
        } elseif ($lowest_bin > 10000 & $lowest_bin < 50000) {
            $sell_bin = $sell_percentage / 100 * $lowest_bin;
            $sell_bin = 250 * round($sell_bin / 250);
            $sell_bid = $sell_bin - 250;
            $new_bin = $buy_percentage / 100 * $lowest_bin;
            $buy_bin = floor($new_bin / 250) * 250;
        } elseif ($lowest_bin > 50000 & $lowest_bin < 100000) {
            $sell_bin = $sell_percentage / 100 * $lowest_bin;
            $sell_bin = 500 * round($sell_bin / 500);
            $sell_bid = $sell_bin - 500;
            $new_bin = $buy_percentage / 100 * $lowest_bin;
            $buy_bin = floor($new_bin / 500) * 500;
        } else {
            $sell_bin = $sell_percentage / 100 * $lowest_bin;
            $sell_bin = 1000 * round($sell_bin / 1000);
            $sell_bid = $sell_bin - 1000;
            $new_bin = $buy_percentage / 100 * $lowest_bin;
            $buy_bin = floor($new_bin / 1000) * 1000;
        }
        return [
            "max_bin" => $buy_bin,
            "sell_bid" => $sell_bid,
            "sell_bin" => $sell_bin
        ];
    }
}
