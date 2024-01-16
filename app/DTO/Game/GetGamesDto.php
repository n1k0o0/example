<?php

namespace App\DTO\Game;

use App\DTO\Casts\CarbonCaster;
use Illuminate\Support\Carbon;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\DataTransferObject;

class GetGamesDto extends DataTransferObject
{
    //SomeCode


    #[CastWith(CarbonCaster::class)]
    public ?Carbon $date;

    #[CastWith(CarbonCaster::class)]
    public ?Carbon $date_to;

    #[CastWith(CarbonCaster::class)]
    public ?Carbon $date_from;

}
