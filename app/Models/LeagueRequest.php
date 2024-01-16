<?php

namespace App\Models;

use App\Enums\LeagueRequest\LeagueRequestStatusEnum;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class LeagueRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        //SomeCode

    ];
    protected $casts = [
        //SomeCode

    ];


    /** @noinspection PhpUnused */
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', LeagueRequestStatusEnum::ACCEPTED->value);
    }
}
