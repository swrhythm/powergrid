<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class player extends Model
{
    use HasFactory;
    protected $fillable = ["name","color","type","passCode","houseCount","largestPowerplant","moderatorId"];
}
