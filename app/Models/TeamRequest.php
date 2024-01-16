<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Carbon;

class TeamRequest extends Model
{
    use HasFactory;

    public const POSITIONS = [
        'ГК',
        'ЛЗ',
        'ПЗ',
        'ЦЗ',
        'ПФЗ',
        'ЛФЗ',
        'ЦОП',
        'ЦП',
        'ЦАП',
        'ПП',
        'ЛП',
        'ПН',
        'ЦН',
        'ЛН',
    ];

    protected $fillable = [
        //SomeCode

    ];
    protected $casts = [
        //SomeCode

    ];

    //SomeCode

}
