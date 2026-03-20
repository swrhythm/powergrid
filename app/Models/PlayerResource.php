<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerResource extends Model
{
    protected $fillable = ['player_id', 'game_session_id', 'coal', 'oil', 'garbage', 'uranium'];

    /**
     * Calculate max storage per resource type based on player's owned powerplants.
     * Returns ['coal'=>n, 'oil'=>n, 'garbage'=>n, 'uranium'=>n]
     */
    public static function calcMaxStorage(int $playerId, int $gameSessionId): array
    {
        $maxStorage = ['coal' => 0, 'oil' => 0, 'garbage' => 0, 'uranium' => 0];

        $powerplants = PlayerPowerplant::where([
            'player_id'       => $playerId,
            'game_session_id' => $gameSessionId,
        ])->with('card')->get();

        foreach ($powerplants as $pp) {
            $card = $pp->card;
            if (!$card || $card->fuel_type === 'eco') continue;

            if ($card->fuel_type === 'hybrid') {
                $maxStorage['coal'] += $card->storage;
                $maxStorage['oil']  += $card->storage;
            } else {
                $maxStorage[$card->fuel_type] += $card->storage;
            }
        }

        return $maxStorage;
    }
}
