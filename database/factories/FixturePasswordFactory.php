<?php

namespace Database\Factories;

use App\Enums\TournamentTypeEnum;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class FixturePasswordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value]);
        return [
            'fixture' => $this->faker->numberBetween(1, 10),
            'password' => null,
            'tournament_id' => $tournament->id,
        ];
    }

    public function withPassword(): FixturePasswordFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'password' => Hash::make('password'),
            ];
        });
    }
}
