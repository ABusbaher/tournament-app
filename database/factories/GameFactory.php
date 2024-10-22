<?php

namespace Database\Factories;

use App\Enums\TournamentTypeEnum;
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
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value, 'rounds' => 2]);
        return [
            'fixture' => $this->faker->numberBetween(1, 10),
            'host_team_id' => function () use ($tournament) {
                return Team::factory()->create(['tournament_id' => $tournament->id])->id;
            },
            'guest_team_id' => function () use ($tournament) {
                return Team::factory()->create(['tournament_id' => $tournament->id])->id;
            },
            'tournament_id' => $tournament->id,
        ];
    }

    public function withScore(): GameFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'host_goals' => $this->faker->numberBetween(0, 4),
                'guest_goals' => $this->faker->numberBetween(0, 4),
            ];
        });
    }
}
