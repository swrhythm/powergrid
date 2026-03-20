<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePowerplantCardsTable extends Migration
{
    public function up()
    {
        Schema::create('powerplant_cards', function (Blueprint $table) {
            $table->integer('number')->primary();
            $table->string('fuel_type'); // coal, oil, garbage, uranium, hybrid, eco
            $table->integer('fuel_needed'); // resources needed to run (0 for eco)
            $table->integer('cities');     // cities it can power
            $table->integer('storage');    // max storable (= fuel_needed * 2)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('powerplant_cards');
    }
}
