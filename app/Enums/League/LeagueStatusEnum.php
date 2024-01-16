<?php

namespace App\Enums\League;

enum LeagueStatusEnum: int
{
    case NOT_STARTED = 1; # планируется
    case CURRENT = 2; # текущий
    case ARCHIVED = 3; # архивный
}
