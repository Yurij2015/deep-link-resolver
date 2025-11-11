<?php

namespace Database\Factories;

use App\Models\Battle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Battle>
 */
class BattleFactory extends Factory
{
    protected $model = Battle::class;

    public function definition(): array
    {
        return [
            'category' => $this->faker->randomElement(['miss_fit', 'casual', 'large', 'special_category']),
            'started_at' => now(),
            'finished_at' => null,
            'candidates' => [
                [
                    'id' => $this->faker->numberBetween(1, 1000),
                    'name' => $this->faker->firstName(),
                    'country' => strtoupper($this->faker->countryCode()),
                    'score' => $this->faker->numberBetween(0, 200)
                ],
                [
                    'id' => $this->faker->numberBetween(1001, 2000),
                    'name' => $this->faker->firstName(),
                    'country' => strtoupper($this->faker->countryCode()),
                    'score' => $this->faker->numberBetween(0, 200)
                ],
            ],
        ];
    }
}
