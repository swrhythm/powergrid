<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('playerId');
            $table->integer('total');
            $table->string('description')->default('-')->nullable();
            $table->string('houseCount')->default('-')->nullable();
            $table->string('powerPlantNumber')->default('-')->nullable();
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
        Schema::dropIfExists('player_transactions');
    }
}
