<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    protected $fillable = [
        'moderator_id', 'step', 'player_count',
        'coal_slots', 'oil_slots', 'garbage_slots', 'uranium_slots'
    ];

    protected $casts = [
        'coal_slots'    => 'array',
        'oil_slots'     => 'array',
        'garbage_slots' => 'array',
        'uranium_slots' => 'array',
    ];

    // Standard initial market state per Powergrid rules
    public static function initialMarket(): array
    {
        return [
            'coal_slots'    => [3, 3, 3, 3, 3, 3, 3, 3],      // slots 1-8, 24 coal
            'oil_slots'     => [0, 0, 3, 3, 3, 3, 3, 3],      // slots 1-8, 18 oil (1-2 empty)
            'garbage_slots' => [0, 0, 0, 0, 0, 0, 3, 3],      // slots 1-8, 6 garbage (1-6 empty)
            'uranium_slots' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1], // slots 1-16, 2 uranium
        ];
    }

    // Slot constraints: [minIndex, maxIndex, maxPerSlot]
    private static function slotConfig(string $type): array
    {
        return match($type) {
            'coal'    => ['min' => 0, 'max' => 7,  'maxPerSlot' => 3],
            'oil'     => ['min' => 2, 'max' => 7,  'maxPerSlot' => 3],
            'garbage' => ['min' => 6, 'max' => 7,  'maxPerSlot' => 3],
            'uranium' => ['min' => 0, 'max' => 15, 'maxPerSlot' => 1],
        };
    }

    /**
     * Calculate cost to buy $qty of $type from market (cheapest first).
     * Does NOT modify the model - call deductResource separately.
     */
    public function calcBuyCost(string $type, int $qty): array
    {
        $slots = $this->{$type . '_slots'};
        $config = self::slotConfig($type);
        $totalCost = 0;
        $remaining = $qty;
        $breakdown = [];

        for ($i = $config['min']; $i <= $config['max'] && $remaining > 0; $i++) {
            $available = $slots[$i];
            $take = min($available, $remaining);
            if ($take > 0) {
                $price = $i + 1; // slot index 0 = slot 1 = 1 Elektro
                $totalCost += $take * $price;
                $breakdown[] = "$take × {$price}E";
                $remaining -= $take;
            }
        }

        return [
            'cost'      => $totalCost,
            'canAfford' => $remaining === 0,
            'breakdown' => implode(' + ', $breakdown),
        ];
    }

    /**
     * Deduct $qty resources of $type from market slots (cheapest first).
     * Returns total cost paid.
     */
    public function deductResource(string $type, int $qty): int
    {
        $slots = $this->{$type . '_slots'};
        $config = self::slotConfig($type);
        $totalCost = 0;
        $remaining = $qty;

        for ($i = $config['min']; $i <= $config['max'] && $remaining > 0; $i++) {
            $take = min($slots[$i], $remaining);
            $totalCost += $take * ($i + 1);
            $slots[$i] -= $take;
            $remaining -= $take;
        }

        $this->{$type . '_slots'} = $slots;
        return $totalCost;
    }

    /**
     * Available supply of a resource type in the market.
     */
    public function availableSupply(string $type): int
    {
        $slots = $this->{$type . '_slots'};
        return array_sum($slots);
    }

    /**
     * Cheapest available price for a resource type.
     */
    public function cheapestPrice(string $type): ?int
    {
        $slots = $this->{$type . '_slots'};
        $config = self::slotConfig($type);
        for ($i = $config['min']; $i <= $config['max']; $i++) {
            if ($slots[$i] > 0) return $i + 1;
        }
        return null;
    }

    /**
     * Refill market based on current step and player count.
     * Adds resources from most expensive slots inward.
     */
    public function refillMarket(): void
    {
        $amounts = $this->getRefillAmounts();

        foreach (['coal', 'oil', 'garbage', 'uranium'] as $type) {
            $amount = $amounts[$type];
            if ($amount <= 0) continue;

            $slots = $this->{$type . '_slots'};
            $config = self::slotConfig($type);

            // Fill from most expensive (right) to least expensive (left)
            for ($i = $config['max']; $i >= $config['min'] && $amount > 0; $i--) {
                $canAdd = $config['maxPerSlot'] - $slots[$i];
                $add = min($canAdd, $amount);
                $slots[$i] += $add;
                $amount -= $add;
            }

            $this->{$type . '_slots'} = $slots;
        }
    }

    /**
     * Refill amounts per resource based on step and player count.
     * Source: official Powergrid rules.
     */
    public function getRefillAmounts(): array
    {
        $s = $this->step;
        $p = $this->player_count;

        $table = [
            // [step][players] => [coal, oil, garbage, uranium]
            1 => [2 => [3,2,1,1], 3 => [4,2,2,1], 4 => [5,3,3,1], 5 => [5,4,3,1], 6 => [7,5,3,1]],
            2 => [2 => [4,3,2,1], 3 => [5,3,3,1], 4 => [6,4,3,2], 5 => [7,5,4,2], 6 => [9,6,5,2]],
            3 => [2 => [3,4,3,1], 3 => [3,5,3,1], 4 => [4,6,3,1], 5 => [5,7,5,1], 6 => [6,9,6,2]],
        ];

        $amounts = $table[$s][$p] ?? [5, 4, 3, 1];

        return [
            'coal'    => $amounts[0],
            'oil'     => $amounts[1],
            'garbage' => $amounts[2],
            'uranium' => $amounts[3],
        ];
    }
}
