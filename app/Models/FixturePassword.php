<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $tournament_id
 * @property int $fixture
 * @property mixed|null $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tournament $tournament
 * @method static \Illuminate\Database\Eloquent\Builder|FixturePassword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FixturePassword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FixturePassword query()
 * @method static \Illuminate\Database\Eloquent\Builder|FixturePassword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FixturePassword whereFixture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FixturePassword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FixturePassword wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FixturePassword whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FixturePassword whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FixturePassword extends Model
{
    use HasFactory;

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    protected $guarded = [];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
