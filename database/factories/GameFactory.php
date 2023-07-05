<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fixture' => $this->faker->numberBetween(1, 10),
            'host_team_id' => function () {
                return Team::factory()->create()->id;
            },
            'guest_team_id' => function () {
                return Team::factory()->create()->id;
            },
            'tournament_id' => function () {
                return Tournament::factory()->create()->id;
            },
        ];
    }
}
