<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->integer('moderator_id');
            $table->integer('step')->default(1); // 1, 2, or 3
            $table->integer('player_count')->default(4); // 2-6
            // 8-slot arrays for coal/oil/garbage (slot index 0 = slot 1 = price 1 Elektro)
            $table->json('coal_slots');    // max 3 per slot, slots 1-8
            $table->json('oil_slots');     // max 3 per slot, slots 1-8 (slots 1-2 always 0)
            $table->json('garbage_slots'); // max 3 per slot, slots 1-8 (slots 1-6 always 0)
            $table->json('uranium_slots'); // max 1 per slot, slots 1-16
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_sessions');
    }
}
