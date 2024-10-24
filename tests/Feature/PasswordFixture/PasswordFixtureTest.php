<?php

namespace Tests\Feature\PasswordFixture;

use App\Enums\TournamentTypeEnum;
use App\Models\FixturePassword;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordFixtureTest extends TestCase
{
    use RefreshDatabase;

    protected Tournament $tournament;
    protected int $fixture;
    protected array $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value]);
        $this->fixture = 1;
        $this->data = [
            'current_password' => 'test_password',
            'new_password' => 'password1',
            'confirm_new_password' => 'password1',
        ];

        FixturePassword::factory()->create([
            'tournament_id' => $this->tournament->id,
            'fixture' => $this->fixture,
            'password' => Hash::make('test_password'),
        ]);
    }

    public function test_guest_can_not_update_fixture_password(): void
    {
        $response = $this->patch(route('game.set.fixture.password', ['tournament' => $this->tournament, 'fixture' => $this->fixture]),
            $this->data);

        $response->assertStatus(403);
    }

    public function test_regular_user_can_not_fixture_password(): void
    {
        $this->signInUser();
        $response = $this->patch(route('game.set.fixture.password', ['tournament' => $this->tournament, 'fixture' => $this->fixture]),
            $this->data);

        $response->assertStatus(403);
    }

    public function test_fixture_password_can_not_be_updated_if_password_not_correct(): void
    {
        $this->signInAdmin();
        $this->data['current_password'] = 'wrong_password';
        $response = $this->patch(route('game.set.fixture.password', ['tournament' => $this->tournament, 'fixture' => $this->fixture]),
            $this->data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Wrong password!',
        ]);
    }

    public function test_fixture_password_can_not_be_updated_if_new_and_confirm_new_password_are_not_the_same(): void
    {
        $this->signInAdmin();
        $this->data['new_password'] = 'password1';
        $this->data['confirm_new_password'] = 'not_same_password';
        $response = $this->patch(route('game.set.fixture.password', ['tournament' => $this->tournament, 'fixture' => $this->fixture]),
            $this->data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'confirm_new_password' => ['The confirm new password field must match new password.'],
        ]);
    }

    public function test_fixture_password_can_be_updated(): void
    {
        $this->signInAdmin();
        $response = $this->patch(route('game.set.fixture.password', ['tournament' => $this->tournament, 'fixture' => $this->fixture]),
            $this->data);

        $response->assertStatus(200);
        // Verify the updated password
        $updatedFixturePassword = FixturePassword::where('tournament_id', $this->tournament->id)
            ->where('fixture', $this->fixture)
            ->first();
        $this->assertTrue(Hash::check($this->data['new_password'], $updatedFixturePassword->password));
    }

    public function test_fixture_password_can_be_updated_to_null(): void
    {
        $this->signInAdmin();
        $this->data['new_password'] = null;
        $this->data['confirm_new_password'] = null;
        $response = $this->patch(route('game.set.fixture.password', ['tournament' => $this->tournament, 'fixture' => $this->fixture]),
            $this->data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('fixture_passwords', [
            'tournament_id' => $this->tournament->id,
            'fixture' => $this->fixture,
            'password' => null,
        ]);
    }

    public function test_fixture_password_can_be_logged_in_with_valid_credentials(): void
    {
        $this->data['password'] = $this->data['current_password'];
        $response = $this->post(route('game.fixture.checkPassword', ['tournament' => $this->tournament, 'fixture' => $this->fixture]),
            $this->data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'password' => 'valid',
        ]);
    }

    public function test_request_password_can_not_be_null(): void
    {
        $this->data['password'] = null;
        $response = $this->post(route('game.fixture.checkPassword', ['tournament' => $this->tournament, 'fixture' => $this->fixture]),
            $this->data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'password' => ['The password field is required.'],
        ]);
    }

    public function test_request_password_can_not_be_less_than_three_characters(): void
    {
        $this->data['password'] = '12';
        $response = $this->post(route('game.fixture.checkPassword', ['tournament' => $this->tournament, 'fixture' => $this->fixture]),
            $this->data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'password' => ['The password field must be at least 3 characters.'],
        ]);
    }
}
