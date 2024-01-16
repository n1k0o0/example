<?php

namespace App\DTO\Game;

use App\DTO\Casts\CarbonCaster;
use Illuminate\Support\Carbon;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class CreateGameDto extends DataTransferObject
{

    #[CastWith(ArrayCaster::class, CreateGamesArrayDto::class)]
    public array $games;

    public ?int $places;

    #[CastWith(CarbonCaster::class)]
    public ?Carbon $started_at;

    #[CastWith(CarbonCaster::class)]
    public ?Carbon $ended_at;

}
