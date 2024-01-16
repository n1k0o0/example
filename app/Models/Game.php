<?php
/** @noinspection PhpUnused */

namespace App\Models;

use App\Enums\Game\GameStatusEnum;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        //SomeCode

    ];
    protected $casts = [
        //SomeCode

    ];



    public function currentMinute(): float
    {
        return ceil((now()->diffInSeconds($this->actual_start_time) - $this->pauses()->sum('duration')) / 60);
    }

    //SomeCode


    /**
     * @return HasMany
     */
    public function pauses(): HasMany
    {
        return $this->hasMany(GamePause::class);
    }

    /**
     * @return HasOne
     */
    public function playerOfTheMatch(): HasOne
    {
        return $this->hasOne(PlayerOfTheMatch::class);
    }

    public function isNotStarted(): bool
    {
        return $this->status === GameStatusEnum::NOT_STARTED;
    }

    public function isStarted(): bool
    {
        return $this->status === GameStatusEnum::STARTED;
    }

    public function isFinished(): bool
    {
        return $this->status === GameStatusEnum::FINISHED;
    }
}
