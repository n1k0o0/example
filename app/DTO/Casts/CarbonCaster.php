<?php

namespace App\DTO\Casts;

use Illuminate\Support\Carbon;
use Spatie\DataTransferObject\Caster;

class CarbonCaster implements Caster
{
    public function cast(mixed $value): Carbon|null
    {
        return Carbon::make($value);
    }
}
