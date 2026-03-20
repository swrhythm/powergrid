<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerPowerplant extends Model
{
    protected $fillable = ['player_id', 'game_session_id', 'card_number'];

    public function card()
    {
        return $this->belongsTo(PowerplantCard::class, 'card_number', 'number');
    }
}
