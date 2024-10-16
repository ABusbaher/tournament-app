<?php

namespace Tests\Feature\Tournament;

use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class TournamentTest extends TestCase
{
    use RefreshDatabase;

    private  function createTournament(): TestResponse
    {
        $this->signInAdmin();
        return $this->postWithCsrfToken(route('tournament.store', [
            'name' => 'PES',
            'rounds' => 2,
            'type' => 'league',
        ]));
    }

    public function test_showing_all_tournaments(): void
    {
        $response = $this->get(route('tournament.all'));

        $response->assertViewIs('tournament.all');
        $response->assertStatus(200);
    }

    public function test_tournament_can_be_created_by_admin(): void
    {
        $response = $this->createTournament();

        $response->assertStatus(201);
        $this->assertDatabaseHas('tournaments', [
            'name' => 'PES',
            'rounds' => 2,
            'type' => 'league'
        ]);
    }

    public function test_tournament_can_not_be_created_if_name_is_not_provided(): void
    {
        $this->signInAdmin();
        $response = $this->postWithCsrfToken(route('tournament.store', [
            'rounds' => 2,
            'type' => 'league',
        ]));

        $response->assertInvalid(['name']);
        $response->assertStatus(422);
    }

    public function test_tournament_can_not_be_created_if_rounds_has_not_between_one_and_four(): void
    {
        $this->signInAdmin();
        $response = $this->postWithCsrfToken(route('tournament.store', [
            'name' => 'PES league',
            'rounds' => 7,
            'type' => 'league',
        ]));

        $response->assertInvalid(['rounds']);
        $response->assertStatus(422);
    }

    public function test_tournament_can_not_be_created_if_type_is_incorrect(): void
    {
        $this->signInAdmin();
        $response = $this->postWithCsrfToken(route('tournament.store', [
            'name' => 'PES league',
            'rounds' => 2,
            'type' => 'not-valid-type',
        ]));

        $response->assertInvalid(['type']);
        $response->assertStatus(422);
    }

    public function test_tournament_can_not_be_created_if_name_is_less_than_three_character(): void
    {
        $this->signInAdmin();
        $response = $this->postWithCsrfToken(route('tournament.store', [
            'name' => 'PE',
            'rounds' => 2,
            'type' => 'league',
        ]));

        $response->assertInvalid(['name']);
        $response->assertStatus(422);
    }

    public function test_single_tournament_can_be_fetched_with_proper_id(): void
    {
        $this->createTournament();
        $response = $this->get(route('tournament.show', ['tournament' => 1]));

        $response->assertStatus(200)->assertJsonFragment([
            'name' => 'PES',
            'rounds' => 2,
            'type' => 'league',
        ]);
    }

    public function test_single_tournament_can_not_be_fetched_with_invalid_id(): void
    {
        $response = $this->get(route('tournament.show', ['tournament' => 'not-valid-id']));
        $response->assertStatus(404);
    }

    public function test_tournament_name_can_be_edited_by_admin_other_properties_stays_unchanged(): void
    {
        $this->createTournament();
        $response = $this->patch(route('tournament.updateName', ['tournament' => 1]), [
            'name' => 'PES updated',
            'rounds' => 2343,
            'type' => 'elimination',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
            'name' => 'PES updated',
            'rounds' => 2,
            'type' => 'league',
        ]);
    }

    public function test_tournament_can_not_be_edited_if_name_is_not_provided(): void
    {
        $this->createTournament();
        $response = $this->patch(route('tournament.updateName', ['tournament' => 1]), [
            'rounds' => 2343,
            'type' => 'elimination',
        ]);

        $response->assertInvalid(['name']);
        $response->assertStatus(422);
    }

    public function test_tournament_can_be_deleted_when_valid_id_is_provided() :void
    {
        $this->createTournament();
        $response = $this->delete(route('tournament.destroy', ['tournament' => 1]));

        $response->assertStatus(204);
    }

    public function test_tournament_cannot_be_deleted_when_invalid_id_is_provided() :void
    {
        $this->createTournament();
        $response = $this->delete(route('tournament.destroy', ['tournament' => 'not-valid-id']));

        $response->assertStatus(404);
    }

    public function test_tournament_cannot_be_created_updated_or_deleted_when_guest_user() :void
    {
        $this->signInUser();
        $tournament = Tournament::factory()->create();
        $createTournamentResponse = $this->postWithCsrfToken(route('tournament.store', [
            'name' => 'PES',
            'rounds' => 2,
            'type' => 'league',
        ]));
        $createTournamentResponse->assertStatus(403);

        $updateTournamentResponse = $this->patch(route('tournament.updateName', ['tournament' => $tournament->id]), [
            'name' => 'PES updated',
            'rounds' => 2343,
            'type' => 'elimination',
        ]);
        $updateTournamentResponse->assertStatus(403);

        $deleteTournamentResponse = $this->delete(route('tournament.destroy', ['tournament' => $tournament->id]));
        $deleteTournamentResponse->assertStatus(403);
    }

    public function test_tournament_cannot_be_created_updated_or_deleted_when_regular_user() :void
    {
        $tournament = Tournament::factory()->create();
        $createTournamentResponse = $this->postWithCsrfToken(route('tournament.store', [
            'name' => 'PES',
            'rounds' => 2,
            'type' => 'league',
        ]));
        $createTournamentResponse->assertStatus(403);

        $updateTournamentResponse = $this->patch(route('tournament.updateName', ['tournament' => $tournament->id]), [
            'name' => 'PES updated',
            'rounds' => 2343,
            'type' => 'elimination',
        ]);
        $updateTournamentResponse->assertStatus(403);

        $deleteTournamentResponse = $this->delete(route('tournament.destroy', ['tournament' => $tournament->id]));
        $deleteTournamentResponse->assertStatus(403);
    }
}
