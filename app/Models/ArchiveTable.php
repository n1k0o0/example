<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ArchiveTable
 *
 * @property int $id
 * @property string $team_name
 * @property int|null $team_id
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
 * @property-read Group $group
 * @property-read League $league
 * @property-read LeagueRequest|null $leagueRequest
 * @method static Builder|ArchiveTable newModelQuery()
 * @method static Builder|ArchiveTable newQuery()
 * @method static Builder|ArchiveTable query()
 * @method static Builder|ArchiveTable whereCreatedAt($value)
 * @method static Builder|ArchiveTable whereDefeat($value)
 * @method static Builder|ArchiveTable whereDraw($value)
 * @method static Builder|ArchiveTable whereGameCount($value)
 * @method static Builder|ArchiveTable whereGoals($value)
 * @method static Builder|ArchiveTable whereGroupId($value)
 * @method static Builder|ArchiveTable whereId($value)
 * @method static Builder|ArchiveTable whereLeagueId($value)
 * @method static Builder|ArchiveTable whereMissedGoals($value)
 * @method static Builder|ArchiveTable whereScore($value)
 * @method static Builder|ArchiveTable whereTeamId($value)
 * @method static Builder|ArchiveTable whereTeamName($value)
 * @method static Builder|ArchiveTable whereUpdatedAt($value)
 * @method static Builder|ArchiveTable whereWin($value)
 * @mixin Eloquent
 */
class ArchiveTable extends Model
{
    use HasFactory;

    protected $fillable = [
        //SomeCode

    ];
    protected $casts = [
        //SomeCode

    ];

    //SomeCode



}
