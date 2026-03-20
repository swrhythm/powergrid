<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerplantCard extends Model
{
    protected $primaryKey = 'number';
    public $incrementing = false;

    protected $fillable = ['number', 'fuel_type', 'fuel_needed', 'cities', 'storage'];

    /**
     * Returns the fuel types that count toward a player's storage capacity for a given resource.
     * Hybrid (coal/oil) plants count for both coal and oil.
     */
    public static function resourceTypes(): array
    {
        return ['coal', 'oil', 'garbage', 'uranium'];
    }

    public function acceptsResource(string $resourceType): bool
    {
        if ($this->fuel_type === 'hybrid') {
            return in_array($resourceType, ['coal', 'oil']);
        }
        return $this->fuel_type === $resourceType;
    }

    public function fuelLabel(): string
    {
        return match($this->fuel_type) {
            'coal'    => '🪨 Coal',
            'oil'     => '🛢 Oil',
            'garbage' => '🗑 Garbage',
            'uranium' => '☢ Uranium',
            'hybrid'  => '🪨/🛢 Coal/Oil',
            'eco'     => '🌿 Eco',
            default   => $this->fuel_type,
        };
    }
}
