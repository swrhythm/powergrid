<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class playerTransaction extends Model
{
    use HasFactory;
    protected $fillable = ["playerId","total","description","houseCount","powerPlantNumber"];
}
