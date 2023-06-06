<?php

namespace Tests\Feature\Tournament;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentTest extends TestCase
{
    use RefreshDatabase;

    public function test_showing_all_tournaments(): void
    {
        $response = $this->get('/tournaments');

        $response->assertViewIs('tournament.all');
        $response->assertStatus(200);
    }

    public function test_tournament_can_be_created(): void
    {
//        $this->withoutMiddleware(VerifyCsrfToken::class);
        $response = $this->postWithCsrfToken('/tournaments', [
            'name' => 'PES league',
            'rounds' => 2,
            'type' => 'league',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tournaments', [
            'name' => 'PES league',
            'rounds' => 2,
            'type' => 'league'
        ]);
    }

    public function test_tournament_can_not_be_created_if_name_is_not_provided(): void
    {
        $response = $this->postWithCsrfToken('/tournaments', [
            'rounds' => 2,
            'type' => 'league',
        ]);

        $response->assertInvalid(['name']);
        $response->assertStatus(422);
    }

    public function test_tournament_can_not_be_created_if_rounds_has_not_between_one_and_four(): void
    {
        $response = $this->postWithCsrfToken('/tournaments', [
            'name' => 'PES league',
            'rounds' => 7,
            'type' => 'league',
        ]);

        $response->assertInvalid(['rounds']);
        $response->assertStatus(422);
    }

    public function test_tournament_can_not_be_created_if_type_is_incorrect(): void
    {
        $response = $this->postWithCsrfToken('/tournaments', [
            'name' => 'PES league',
            'rounds' => 2,
            'type' => 'not-valid-type',
        ]);

        $response->assertInvalid(['type']);
        $response->assertStatus(422);
    }

    public function test_tournament_can_not_be_created_if_name_is_less_than_three_character(): void
    {
        $response = $this->postWithCsrfToken('/tournaments', [
            'name' => 'PE',
            'rounds' => 2,
            'type' => 'league',
        ]);

        $response->assertInvalid(['name']);
        $response->assertStatus(422);
    }
}
