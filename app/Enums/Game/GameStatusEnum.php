<?php

namespace App\Enums\Game;

enum GameStatusEnum: string
{
    case NOT_STARTED = 'not_started'; # не начато
    case STARTED = 'started'; # начато
    case FINISHED = 'finished'; # завершено
}
