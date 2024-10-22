<?php

namespace Database\Factories;

use App\Enums\TournamentTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'type' => fake()->randomElement(TournamentTypeEnum::cases())->value,
            'rounds' => fake()->numberBetween(1, 2),
        ];
    }
}
