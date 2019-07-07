<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Item;
use App\Helpers;
use App\Configuration;

class PricesUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pricesupdate:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that update new player prices from futbin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $items = Item::all();
        foreach($items as $item)
        {
            $configuration = Configuration::where('user_id', $item->user_id)->first();

            $prices = Helpers::getPrices($item->asset_id);
            $pricesXBOX = json_decode(json_encode(Helpers::calculatePrices($prices[0], $configuration->buy_percentage, $configuration->sell_percentage)));
            $pricesPS = json_decode(json_encode(Helpers::calculatePrices($prices[1], $configuration->buy_percentage, $configuration->sell_percentage)));
            $pricesPC = json_decode(json_encode(Helpers::calculatePrices($prices[2], $configuration->buy_percentage, $configuration->sell_percentage)));
            $item->xbox_buy_bin = $pricesXBOX->max_bin;
            $item->ps_buy_bin = $pricesPS->max_bin;
            $item->pc_buy_bin = $pricesPC->max_bin;
            $item->xbox_sell_bin = $pricesXBOX->sell_bin;
            $item->ps_sell_bin = $pricesPS->sell_bin;
            $item->pc_sell_bin = $pricesPC->sell_bin;

            $item->save();
            
            $this->info("Item price updated [" . $item->name .", " . $item->xbox_buy_bin . " (XBOX), ". $item->ps_buy_bin ." (PS), " . $item->pc_buy_bin . " (PC)]");
        }
    }
}
