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
            $table->string('backupCodes')->nullable()->default(NULL);
            $table->integer('user_id');
            $table->integer('minutesRunning')->default(0);
            $table->string('phishingToken')->nullable()->default(NULL);
            $table->string('personaId')->nullable()->default(NULL);
            $table->string('personaName')->nullable()->default(NULL);
            $table->string('nucleusId')->nullable()->default(NULL);
            $table->string('clubName')->nullable()->default(NULL);
            $table->string('sessionId')->nullable()->default(NULL);
            $table->string('coins')->nullable()->default(NULL);
            $table->string('tradepile_limit')->nullable()->default(NULL);
            $table->string('dob')->nullable()->default(NULL);
            $table->string('last_login')->nullable()->default(NULL);
            $table->integer('status')->nullable()->default(NULL);
            $table->string('status_reason')->nullable()->default(NULL);
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
