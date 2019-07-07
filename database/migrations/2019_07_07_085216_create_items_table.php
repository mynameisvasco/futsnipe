<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('asset_id');
            $table->integer('pc_buy_bin');
            $table->integer('ps_buy_bin');
            $table->integer('xbox_buy_bin');
            $table->integer('pc_sell_bin');
            $table->integer('ps_sell_bin');
            $table->integer('xbox_sell_bin');
            $table->string('type');
            $table->integer('rating');
            $table->string('name');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
