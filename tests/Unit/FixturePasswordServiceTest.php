<?php

namespace Tests\Unit;

use App\Enums\TournamentTypeEnum;
use App\Models\FixturePassword;
use App\Models\Tournament;
use App\Services\FixturePasswordService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FixturePasswordServiceTest extends TestCase
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

    public function test_exception_is_thrown_when_wrong_tournament_type(): void
    {
        $this->tournament->type = TournamentTypeEnum::ELIMINATION->value;
        $this->tournament->save();

        $fixturePasswordService = new FixturePasswordService();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Not valid tournament type');
        $this->expectExceptionCode(422);

        $fixturePasswordService->updateFixturePassword($this->data, $this->tournament, $this->fixture);
    }

    public function test_exception_is_thrown_when_not_same_password_tournament_type(): void
    {
        $this->data['current_password'] = 'wrong_password';

        $fixturePasswordService = new FixturePasswordService();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Wrong password!');
        $this->expectExceptionCode(422);

        $fixturePasswordService->updateFixturePassword($this->data, $this->tournament, $this->fixture);
    }

    public function test_game_fixture_password_can_be_updated(): void
    {
        $fixturePasswordService = new FixturePasswordService();
        $fixturePasswordService->updateFixturePassword($this->data, $this->tournament, $this->fixture);

        // Verify the updated password
        $updatedFixturePassword = FixturePassword::where('tournament_id', $this->tournament->id)
            ->where('fixture', $this->fixture)
            ->first();
        $this->assertTrue(Hash::check($this->data['new_password'], $updatedFixturePassword->password));
    }

    public function test_game_fixture_password_can_be_updated_to_null(): void
    {
        $this->data['new_password'] = null;
        $this->data['confirm_new_password'] = null;

        $fixturePasswordService = new FixturePasswordService();
        $fixturePasswordService->updateFixturePassword($this->data, $this->tournament, $this->fixture);

        $this->assertDatabaseHas('fixture_passwords', [
            'tournament_id' => $this->tournament->id,
            'fixture' => $this->fixture,
            'password' => null,
        ]);
    }

    public function test_exception_is_thrown_when_not_valid_password_in_check_fixture_password(): void
    {
        $password = 'wrong_password';

        $fixturePasswordService = new FixturePasswordService();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Wrong password!');
        $this->expectExceptionCode(422);

        $fixturePasswordService->checkFixturePassword($this->tournament, $this->fixture, $password);
    }

    public function test_exception_is_thrown_when_not_valid_fixture_in_check_fixture_password(): void
    {
        $nonExistingFixture = 456544;

        $fixturePasswordService = new FixturePasswordService();

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [App\Models\FixturePassword].');

        $fixturePasswordService->checkFixturePassword($this->tournament, $nonExistingFixture, $this->data['current_password']);
    }

    public function can_login_with_valid_password_in_check_fixture_password(): void
    {
        $password = $this->data['current_password'];

        $fixturePasswordService = new FixturePasswordService();
        $login = $fixturePasswordService->checkFixturePassword($this->tournament, $this->fixture, $password);

        $this->assertTrue($login);
    }
}
