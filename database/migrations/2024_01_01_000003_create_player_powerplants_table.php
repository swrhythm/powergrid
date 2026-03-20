<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerPowerplantsTable extends Migration
{
    public function up()
    {
        Schema::create('player_powerplants', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id');
            $table->integer('game_session_id');
            $table->integer('card_number');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('player_powerplants');
    }
}
