<?php

namespace Tests\Feature\EliminationGame;

use App\Enums\TournamentTypeEnum;
use App\Models\EliminationGame;
use App\Models\Team;
use App\Models\Tournament;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EliminationGameTest extends TestCase
{
    use RefreshDatabase;

    public function test_elimination_games_by_cup_can_be_created_by_admin(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION->value, 'rounds' => 1]);
        Team::factory()->times(6)->create([
            'tournament_id' => $tournament->id,
        ]);

        $this->assertDatabaseMissing('games', [
            'tournament_id' => $tournament->id,
        ]);

        $response = $this->post(route('elimination-games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $response->assertStatus(201);

        $this->assertEquals(5, \DB::table('elimination_games')->count());// assert 5 games is created with 6 teams and 3 rounds of matches.

        // assert correct cup structure is created with 6 6 teams and 3 rounds of matches.
        $this->assertDatabaseHas('elimination_games', [
            'tournament_id' => $tournament->id,
            'round' => 3,
            'next_match' => '2-1'
        ]);
        $this->assertDatabaseHas('elimination_games', [
            'tournament_id' => $tournament->id,
            'round' => 3,
            'next_match' => '2-2'
        ]);
        $this->assertDatabaseHas('elimination_games', [
            'tournament_id' => $tournament->id,
            'round' => 2,
            'next_match' => '3-1'
        ]);
        $this->assertDatabaseHas('elimination_games', [
            'tournament_id' => $tournament->id,
            'round' => 2,
            'next_match' => '3-2'
        ]);
        $this->assertDatabaseHas('elimination_games', [
            'tournament_id' => $tournament->id,
            'round' => 1,
            'next_match' => null
        ]);
    }

    public function test_cup_games_can_not_be_created_if_already_been_created_before(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION, 'rounds' => 2]);
        EliminationGame::factory()->count(6)->create(['tournament_id' => $tournament->id]);

        $response = $this->post(route('elimination-games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'Elimination cup for this tournament already exist!',
        ]);
    }

    public function test_cup_games_can_not_be_created_with_wrong_tournament_type(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value]);
        Team::factory()->times(6)->create([
            'tournament_id' => $tournament->id,
        ]);

        $response = $this->post(route('elimination-games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Not valid tournament type.',
        ]);
    }

    public function test_tournament_can_not_be_updated_if_cup_games_are_already_created(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION]);
        EliminationGame::factory()->count(6)->create(['tournament_id' => $tournament->id]);
        $response = $this->put(route('tournament.updateAll', ['tournament' => $tournament->id]), [
            'name' => 'PES updated',
            'rounds' => 3,
            'type' => TournamentTypeEnum::ELIMINATION->value,
            'tournament_id' => $tournament->id
        ]);
        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'Elimination cup for this tournament already exist!',
        ]);
    }

    public function test_cup_games_can_not_be_created_if_less_than_four_teams_created(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION]);
        Team::factory()->count(3)->create(['tournament_id' => $tournament->id]);
        $response = $this->post(route('elimination-games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $response->assertStatus(422);
        $response->assertInvalid(['tournament_id']);
    }

    public function test_elimination_games_can_be_fetched_by_tournament(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION, 'rounds' => 1]);
        Team::factory()->count(4)->create(['tournament_id' => $tournament->id]);
        $this->post(route('elimination-games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);
        $response = $this->get(route('elimination-games.by_tournament', [
            'tournament' => $tournament->id,
        ]));
        $response->assertStatus(200);

        $response->assertJsonFragment([
            'next_match' => '2-1',
            'tournament_id' => $tournament->id,
            'team1_goals' => NULL,
            'team2_goals' => NULL,
            'round' => 2,
        ]);

        $response->assertJsonFragment([
            'next_match' => '2-2',
            'tournament_id' => $tournament->id,
            'team1_goals' => NULL,
            'team2_goals' => NULL,
            'round' => 2,
        ]);

        $response->assertJsonFragment([
            'team1_id' => NULL,
            'team2_id' => NULL,
            'team1_prev' => '2-1',
            'team2_prev' => '2-2',
            'next_match' => null,
            'tournament_id' => $tournament->id,
            'team1_goals' => NULL,
            'team2_goals' => NULL,
            'round' => 1,
        ]);
        $response->assertJsonFragment([
            'max_round' => 2,
            'non_played_games' => 0,
        ]);
        $response->assertJsonCount(3, $key = 'games');
    }

    public function test_correct_max_round_and_non_played_games_when_fetching_cup_games(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION, 'rounds' => 1]);
        Team::factory()->count(6)->create(['tournament_id' => $tournament->id]);
        $this->post(route('elimination-games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);
        $response = $this->get(route('elimination-games.by_tournament', [
            'tournament' => $tournament->id,
        ]));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'max_round' => 3,
            'non_played_games' => 2,
        ]);

        $tournament2 = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION, 'rounds' => 1]);
        Team::factory()->count(11)->create(['tournament_id' => $tournament2->id]);
        $this->post(route('elimination-games.create.all', ['tournament' => $tournament2->id]),
            ['tournament_id' => $tournament2->id]);
        $response = $this->get(route('elimination-games.by_tournament', [
            'tournament' => $tournament2->id,
        ]));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'max_round' => 4,
            'non_played_games' => 5,
        ]);

        $tournament3 = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION, 'rounds' => 1]);
        Team::factory()->count(19)->create(['tournament_id' => $tournament3->id]);
        $this->post(route('elimination-games.create.all', ['tournament' => $tournament3->id]),
            ['tournament_id' => $tournament3->id]);
        $response = $this->get(route('elimination-games.by_tournament', [
            'tournament' => $tournament3->id,
        ]));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'max_round' => 5,
            'non_played_games' => 13,
        ]);
    }

    public function test_cup_game_can_be_fetched_by_tournament_id_and_game_id(): void
    {
        $this->signInAdmin();
        EliminationGame::factory()->count(4)->create(['tournament_id' => 1]);
        $response = $this->get(route('elimination-game.show', [
            'tournament' => 1,
            'game' => 1,
        ]));
        $response->assertStatus(200);
        $response->assertJson([
            'id' => 1,
            'tournament_id' => 1
        ]);
    }

    public function test_cup_game_can_not_be_fetched_if_wrong_tournament_id_or_game_id(): void
    {
        $this->signInAdmin();
        EliminationGame::factory()->count(4)->create(['tournament_id' => 1]);
        $response = $this->get(route('elimination-game.show', [
            'tournament' => 1,
            'game' => 'none-existing-game-id',
        ]));
        $response->assertStatus(404);
    }

    public function test_elimination_game_score_and_time_can_be_updated_by_admin(): void
    {
        $this->signInAdmin();
        EliminationGame::factory()->count(4)->create(['tournament_id' => 1]);
        $gameTime = new Carbon('2024-05-26');
        $gameTime->setTimeFromTimeString('16:00:00');
        $response = $this->patch(route('elimination-game.updateScore', ['tournament' => 1, 'game' => 1]),
            ['team1_goals' => 2, 'team2_goals' => 0, 'game_time' => $gameTime]
        );
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'team1_goals' => 2,
            'team2_goals' => 0,
            'game_time' => $gameTime->toISOString(),
        ]);
    }

    public function test_elimination_game_score_can_not_be_updated_if_scores_are_equal(): void
    {
        $this->signInAdmin();
        EliminationGame::factory()->count(4)->create(['tournament_id' => 1]);
        $gameTime = new Carbon('2024-05-26');
        $gameTime->setTimeFromTimeString('16:00:00');
        $response = $this->patch(route('elimination-game.updateScore', ['tournament' => 1, 'game' => 1]),
            ['team1_goals' => 2, 'team2_goals' => 2, 'game_time' => $gameTime]
        );
        $response->assertStatus(422);
        $response->assertInvalid(['team1_goals']);
        $response->assertJsonValidationErrors([
            'team1_goals' => 'The team1_goals must be different from team2_goals if both fields have values.',
        ]);
    }

    public function test_when_update_elimination_game_score_with_admin_user_their_next_games_also_updates(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION, 'rounds' => 1]);
        Team::factory()->count(6)->create(['tournament_id' => $tournament->id]);
        $this->post(route('elimination-games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $responseFirstGame = $this->get(route('elimination-game.show', [
            'tournament' => $tournament,
            'game' => 1,
        ]));
        $firstGame = json_decode($responseFirstGame->content());

        $gameTime = new Carbon('2024-05-26');
        $gameTime->setTimeFromTimeString('16:00:00');
        $response = $this->patch(route('elimination-game.updateScore', ['tournament' =>  $tournament->id, 'game' => $firstGame->id]),
            ['team1_goals' => 2, 'team2_goals' => 0, 'game_time' => $gameTime]
        );
        $response->assertStatus(200);
        $this->assertDatabaseHas('elimination_games', [
            'id' => $firstGame->id,
            'tournament_id' => $tournament->id,
            'next_match' => $firstGame->next_match,  //'2-1',
            'team1_id' => $firstGame->team1_id,
            'team2_id' => $firstGame->team2_id,
            'team1_goals' => 2,
            'team2_goals' => 0,
            'game_time' => $gameTime,
            'round' => $firstGame->round,
        ]);
        $this->assertDatabaseHas('elimination_games', [
            'tournament_id' => $tournament->id,
            'team1_id' => $firstGame->team1_id, // winner of first match
            'team1_prev' =>  $firstGame->next_match, //'2-1',
            'next_match' => '3-2', // next game when 6 teams
            'team1_goals' => null,
            'team2_goals' => null,
            'round' => 2
        ]);
        $this->assertDatabaseHas('elimination_games', [
            'tournament_id' => $tournament->id,
            'team2_prev' => '3-2',
            'next_match' => null,
            'team1_goals' => null,
            'team2_goals' => null,
            'round' => 1
        ]);
    }

    public function test_admin_can_access_elimination_games_edit_page(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION, 'rounds' => 1]);
        Team::factory()->count(6)->create(['tournament_id' => $tournament->id]);
        $this->post(route('elimination-games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);
        $response = $this->get(route('admin.elimination.games', ['tournament' => $tournament->id]));
        $response->assertStatus(200);
    }

    public function test_guest_can_not_access_elimination_games_edit_page(): void
    {
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION, 'rounds' => 1]);
        Team::factory()->count(6)->create(['tournament_id' => $tournament->id]);
        $this->post(route('elimination-games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);
        $response = $this->get(route('admin.elimination.games', ['tournament' => $tournament->id]));
        $response->assertStatus(403);
    }

    public function test_regular_user_can_not_access_elimination_games_edit_page(): void
    {
        $this->signInUser();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION, 'rounds' => 1]);
        Team::factory()->count(6)->create(['tournament_id' => $tournament->id]);
        $this->post(route('elimination-games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);
        $response = $this->get(route('admin.elimination.games', ['tournament' => $tournament->id]));
        $response->assertStatus(403);
    }

    public function test_guest_can_not_create_or_edit_elimination_games(): void
    {
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION, 'rounds' => 1]);
        Team::factory()->count(6)->create(['tournament_id' => $tournament->id]);
        $createResponse = $this->post(route('elimination-games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);
        $createResponse->assertStatus(403);

        EliminationGame::factory()->count(4)->create(['tournament_id' => $tournament->id]);
        $gameTime = new Carbon('2024-05-26');
        $gameTime->setTimeFromTimeString('16:00:00');
        $updateResponse = $this->patch(route('elimination-game.updateScore', ['tournament' => $tournament->id, 'game' => 1]),
            ['team1_goals' => 2, 'team2_goals' => 0, 'game_time' => $gameTime]
        );
        $updateResponse->assertStatus(403);
    }

    public function test_regular_user_can_not_create_or_edit_elimination_games(): void
    {
        $this->signInUser();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION, 'rounds' => 1]);
        Team::factory()->count(6)->create(['tournament_id' => $tournament->id]);
        $createResponse = $this->post(route('elimination-games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);
        $createResponse->assertStatus(403);

        EliminationGame::factory()->count(4)->create(['tournament_id' => $tournament->id]);
        $gameTime = new Carbon('2024-05-26');
        $gameTime->setTimeFromTimeString('16:00:00');
        $updateResponse = $this->patch(route('elimination-game.updateScore', ['tournament' => $tournament->id, 'game' => 1]),
            ['team1_goals' => 2, 'team2_goals' => 0, 'game_time' => $gameTime]
        );
        $updateResponse->assertStatus(403);
    }
}
