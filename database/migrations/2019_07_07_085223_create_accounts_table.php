<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email');
            $table->string('password');
            $table->string('platform');
            $table->string('backupCodes');
            $table->integer('user_id');
            $table->string('phishingToken');
            $table->string('personaId');
            $table->string('personaName');
            $table->string('nucleusId');
            $table->string('clubName');
            $table->string('sessionId');
            $table->string('coins');
            $table->string('tradepile_limit');
            $table->string('dob');
            $table->string('last_login');
            $table->integer('status');
            $table->string('status_reason');
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
        Schema::dropIfExists('accounts');
    }
}
