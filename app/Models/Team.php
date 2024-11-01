<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Team
 *
 * @property int $id
 * @property int $tournament_id
 * @property string $name
 * @property string $shorten_name
 * @property string|null $image_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tournament $tournament
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUpdatedAt($value)
 * @method static \Database\Factories\TeamFactory factory($count = null, $state = [])
 * @property int|null $negative_points
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereNegativePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereShortenName($value)
 * @mixin \Eloquent
 */
class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

}
