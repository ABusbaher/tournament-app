<?php

namespace Database\Factories;

use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
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
            'shorten_name' => fake()->regexify('[a-zA-Z]{2,4}'),
            'tournament_id' => function () {
                    return Tournament::factory()->create()->id;
                },
            'image_path' => null,
        ];
    }

    public function withImage(): TeamFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'image_path' => UploadedFile::fake()->image('test-image.jpg')
            ];
        });
    }

}
