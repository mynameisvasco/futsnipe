<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFifaCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fifa_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->integer('rating');
            $table->string('name');
            $table->string('position');
            $table->bigInteger('club');
            $table->bigInteger('nationality');
            $table->bigInteger('asset_id');
            $table->bigInteger('definition_id');
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
        Schema::dropIfExists('fifa_cards');
    }
}
