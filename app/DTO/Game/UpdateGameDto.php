<?php

namespace App\DTO\Game;

use App\DTO\Casts\CarbonCaster;
use Illuminate\Support\Carbon;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;

class UpdateGameDto extends DataTransferObject
{

    //SomeCode

    #[CastWith(CarbonCaster::class)]
    public ?Carbon $started_at;

    #[CastWith(CarbonCaster::class)]
    public ?Carbon $ended_at;

}
