<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerResourcesTable extends Migration
{
    public function up()
    {
        Schema::create('player_resources', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id');
            $table->integer('game_session_id');
            $table->integer('coal')->default(0);
            $table->integer('oil')->default(0);
            $table->integer('garbage')->default(0);
            $table->integer('uranium')->default(0);
            $table->unique(['player_id', 'game_session_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('player_resources');
    }
}
