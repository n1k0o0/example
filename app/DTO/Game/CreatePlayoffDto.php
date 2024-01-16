<?php

namespace App\DTO\Game;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class CreatePlayoffDto extends DataTransferObject
{
    #[CastWith(ArrayCaster::class, CreateGamesArrayDto::class)]
    public array $games;
}
