<?php

namespace App\Models;

use App\Enums\TournamentTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Tournament
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament query()
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int $rounds
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereRounds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @method static \Database\Factories\TournamentFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class Tournament extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'type' => TournamentTypeEnum::class
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function isLeague(): bool
    {
        return $this->type === TournamentTypeEnum::LEAGUE;
    }

    public function isElimination(): bool
    {
        return $this->type === TournamentTypeEnum::ELIMINATION;
    }

    public function setRoundsAttribute(?int $rounds): void
    {
        $this->attributes['rounds'] = $rounds ?? 1;
    }
}
