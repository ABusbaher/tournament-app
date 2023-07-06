<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Game
 *
 * @property int $id
 * @property int $host_team_id
 * @property int|null $guest_team_id
 * @property int $tournament_id
 * @property int|null $host_goals
 * @property int|null $guest_goals
 * @property int $fixture
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Team|null $guestTeam
 * @property-read \App\Models\Team $hostTeam
 * @property-read \App\Models\Tournament $tournament
 * @method static \Database\Factories\GameFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game query()
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereFixture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereGuestGoals($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereGuestTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereHostGoals($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereHostTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Game extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function hostTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'host_team_id');
    }

    public function guestTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'guest_team_id');
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
