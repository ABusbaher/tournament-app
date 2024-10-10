<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\EliminationGame
 *
 * @property int $id
 * @property int|null $team1_id
 * @property int|null $team2_id
 * @property string|null $team1_prev
 * @property string|null $team2_prev
 * @property string|null $next_match
 * @property int $tournament_id
 * @property int|null $team1_goals
 * @property int|null $team2_goals
 * @property \Illuminate\Support\Carbon|null $game_time
 * @property int $round
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Team|null $firstTeam
 * @property-read \App\Models\Team|null $secondTeam
 * @property-read \App\Models\Tournament $tournament
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame query()
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame whereNextMatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame whereRound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame whereTeam1Goals($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame whereTeam1Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame whereTeam1Prev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame whereTeam2Goals($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame whereTeam2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame whereTeam2Prev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EliminationGame whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EliminationGame extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'game_time' => 'datetime',
    ];

    public function firstTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function secondTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
