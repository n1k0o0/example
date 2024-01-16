<?php

namespace App\Enums\LeagueRequest;

enum LeagueRequestStatusEnum: int
{
    case REVIEW = 1; #на рассмотрении
    case ACCEPTED = 2; #одобрен
    case REJECTED = 3; # отклонено
}
