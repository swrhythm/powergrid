<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PowerplantCard;

class PowerplantCardSeeder extends Seeder
{
    public function run()
    {
        // Standard Power Grid (Recharged) card set
        // fuel_type: coal, oil, garbage, uranium, hybrid (coal/oil), eco
        // storage = fuel_needed * 2 (max storable per rulebook)
        $cards = [
            // === STARTING MARKET (cards 3-10) ===
            ['number' => 3,  'fuel_type' => 'coal',    'fuel_needed' => 2, 'cities' => 1],
            ['number' => 4,  'fuel_type' => 'coal',    'fuel_needed' => 2, 'cities' => 1],
            ['number' => 5,  'fuel_type' => 'oil',     'fuel_needed' => 2, 'cities' => 1],
            ['number' => 6,  'fuel_type' => 'garbage', 'fuel_needed' => 1, 'cities' => 1],
            ['number' => 7,  'fuel_type' => 'coal',    'fuel_needed' => 3, 'cities' => 2],
            ['number' => 8,  'fuel_type' => 'oil',     'fuel_needed' => 3, 'cities' => 2],
            ['number' => 9,  'fuel_type' => 'uranium', 'fuel_needed' => 1, 'cities' => 1],
            ['number' => 10, 'fuel_type' => 'coal',    'fuel_needed' => 2, 'cities' => 2],
            // === STAGE 1 CARDS ===
            ['number' => 11, 'fuel_type' => 'garbage', 'fuel_needed' => 1, 'cities' => 2],
            ['number' => 12, 'fuel_type' => 'oil',     'fuel_needed' => 2, 'cities' => 2],
            ['number' => 13, 'fuel_type' => 'eco',     'fuel_needed' => 0, 'cities' => 1],
            ['number' => 14, 'fuel_type' => 'hybrid',  'fuel_needed' => 2, 'cities' => 2],
            ['number' => 15, 'fuel_type' => 'garbage', 'fuel_needed' => 2, 'cities' => 3],
            ['number' => 16, 'fuel_type' => 'coal',    'fuel_needed' => 2, 'cities' => 3],
            ['number' => 17, 'fuel_type' => 'oil',     'fuel_needed' => 2, 'cities' => 3],
            ['number' => 18, 'fuel_type' => 'garbage', 'fuel_needed' => 2, 'cities' => 3],
            ['number' => 19, 'fuel_type' => 'uranium', 'fuel_needed' => 2, 'cities' => 3],
            ['number' => 20, 'fuel_type' => 'coal',    'fuel_needed' => 3, 'cities' => 5],
            // === STAGE 2 CARDS ===
            ['number' => 21, 'fuel_type' => 'hybrid',  'fuel_needed' => 2, 'cities' => 4],
            ['number' => 22, 'fuel_type' => 'eco',     'fuel_needed' => 0, 'cities' => 2],
            ['number' => 23, 'fuel_type' => 'uranium', 'fuel_needed' => 1, 'cities' => 2],
            ['number' => 24, 'fuel_type' => 'coal',    'fuel_needed' => 2, 'cities' => 4],
            ['number' => 25, 'fuel_type' => 'oil',     'fuel_needed' => 2, 'cities' => 4],
            ['number' => 26, 'fuel_type' => 'garbage', 'fuel_needed' => 2, 'cities' => 4],
            ['number' => 27, 'fuel_type' => 'uranium', 'fuel_needed' => 1, 'cities' => 3],
            ['number' => 28, 'fuel_type' => 'hybrid',  'fuel_needed' => 2, 'cities' => 4],
            ['number' => 29, 'fuel_type' => 'uranium', 'fuel_needed' => 1, 'cities' => 4],
            ['number' => 30, 'fuel_type' => 'coal',    'fuel_needed' => 3, 'cities' => 6],
            ['number' => 31, 'fuel_type' => 'oil',     'fuel_needed' => 3, 'cities' => 6],
            ['number' => 32, 'fuel_type' => 'garbage', 'fuel_needed' => 3, 'cities' => 6],
            ['number' => 33, 'fuel_type' => 'eco',     'fuel_needed' => 0, 'cities' => 4],
            ['number' => 34, 'fuel_type' => 'uranium', 'fuel_needed' => 1, 'cities' => 5],
            ['number' => 35, 'fuel_type' => 'oil',     'fuel_needed' => 2, 'cities' => 5],
            // === STAGE 3 CARDS ===
            ['number' => 36, 'fuel_type' => 'coal',    'fuel_needed' => 3, 'cities' => 7],
            ['number' => 37, 'fuel_type' => 'oil',     'fuel_needed' => 3, 'cities' => 7],
            ['number' => 38, 'fuel_type' => 'garbage', 'fuel_needed' => 3, 'cities' => 7],
            ['number' => 39, 'fuel_type' => 'uranium', 'fuel_needed' => 1, 'cities' => 6],
            ['number' => 40, 'fuel_type' => 'eco',     'fuel_needed' => 0, 'cities' => 5],
            ['number' => 42, 'fuel_type' => 'eco',     'fuel_needed' => 0, 'cities' => 6],
            ['number' => 44, 'fuel_type' => 'eco',     'fuel_needed' => 0, 'cities' => 7],
            ['number' => 46, 'fuel_type' => 'eco',     'fuel_needed' => 0, 'cities' => 7],
            ['number' => 50, 'fuel_type' => 'eco',     'fuel_needed' => 0, 'cities' => 7],
        ];

        foreach ($cards as $data) {
            PowerplantCard::updateOrCreate(
                ['number' => $data['number']],
                [
                    'fuel_type'   => $data['fuel_type'],
                    'fuel_needed' => $data['fuel_needed'],
                    'cities'      => $data['cities'],
                    'storage'     => $data['fuel_needed'] * 2,
                ]
            );
        }
    }
}
