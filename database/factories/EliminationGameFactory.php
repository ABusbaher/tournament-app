<?php

namespace Database\Factories;

use App\Models\EliminationGame;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EliminationGame>
 */
class EliminationGameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tournament = Tournament::factory()->create(['type' => 'elimination', 'rounds' => 1]);
        return [
            'round' => $this->faker->numberBetween(1, 5),
            'team1_id' => function () use ($tournament) {
                return Team::factory()->create(['tournament_id' => $tournament->id])->id;
            },
            'team2_id' => function () use ($tournament) {
                return Team::factory()->create(['tournament_id' => $tournament->id])->id;
            },
            'tournament_id' => $tournament->id,
        ];
    }

    public function withScore(): EliminationGameFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'team1_goals' => $this->faker->numberBetween(0, 2),
                'team2_goals' => $this->faker->numberBetween(3, 5),
            ];
        });
    }
}
