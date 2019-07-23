<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Helpers extends Model
{
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
