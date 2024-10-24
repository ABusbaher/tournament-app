<?php

namespace App\Services;

use App\Models\FixturePassword;
use App\Models\Tournament;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class FixturePasswordService
{
    public function checkFixturePassword(Tournament $tournament, int $fixture, string $password): bool
    {
        $fixturePassword = FixturePassword::where('tournament_id', $tournament->id)
            ->where('fixture', $fixture)
            ->firstOrFail();

        if (!Hash::check($password, $fixturePassword->password)) {
            throw new InvalidArgumentException('Wrong password!', 422);
        }
        return true;
    }
    public function updateFixturePassword(array $data, Tournament $tournament, int $fixture): FixturePassword
    {
        if (!$tournament->isLeague()) {
            throw new InvalidArgumentException('Not valid tournament type', 422);
        }
        $fixturePassword = FixturePassword::where('tournament_id', $tournament->id)->where('fixture', $fixture)->firstOrFail();
        $hashedPassword = $data['new_password'] ? Hash::make($data['new_password']) : null;

        if ($fixturePassword->password && !Hash::check($data['current_password'], $fixturePassword->password)) {
            throw new InvalidArgumentException('Wrong password!', 422);
        }

        $fixturePassword->update(['password' => $hashedPassword]);
        return $fixturePassword;
    }

}
