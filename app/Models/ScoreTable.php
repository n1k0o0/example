<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ScoreTable
 *
 * @property int $id
 * @property string $team_name
 * @property int $team_id
 * @property int $league_id
 * @property int $group_id
 * @property int $game_count
 * @property int $win
 * @property int $draw
 * @property int $defeat
 * @property int $goals
 * @property int $missed_goals
 * @property int $score
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ScoreTable newModelQuery()
 * @method static Builder|ScoreTable newQuery()
 * @method static Builder|ScoreTable query()
 * @method static Builder|ScoreTable whereCreatedAt($value)
 * @method static Builder|ScoreTable whereDefeat($value)
 * @method static Builder|ScoreTable whereDraw($value)
 * @method static Builder|ScoreTable whereGameCount($value)
 * @method static Builder|ScoreTable whereGoals($value)
 * @method static Builder|ScoreTable whereGroupId($value)
 * @method static Builder|ScoreTable whereId($value)
 * @method static Builder|ScoreTable whereLeagueId($value)
 * @method static Builder|ScoreTable whereMissedGoals($value)
 * @method static Builder|ScoreTable whereScore($value)
 * @method static Builder|ScoreTable whereTeamId($value)
 * @method static Builder|ScoreTable whereTeamName($value)
 * @method static Builder|ScoreTable whereUpdatedAt($value)
 * @method static Builder|ScoreTable whereWin($value)
 * @mixin Eloquent
 * @property-read Group $group
 * @property-read League $league
 * @property-read LeagueRequest|null $leagueRequest
 */
class ScoreTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_name',
        'team_id',
        'league_id',
        'group_id',
        'game_count',
        'win',
        'draw',
        'defeat',
        'goals',
        'missed_goals',
        'score',
    ];

    protected $casts = [
        'id' => 'integer',
        'team_id' => 'integer',
        'league_id' => 'integer',
        'group_id' => 'integer',
        'game_count' => 'integer',
        'win' => 'integer',
        'draw' => 'integer',
        'defeat' => 'integer',
        'goals' => 'integer',
        'missed_goals' => 'integer',
        'score' => 'integer',
    ];

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function leagueRequest(): BelongsTo
    {
        return $this->belongsTo(LeagueRequest::class, 'team_id');
    }
}
