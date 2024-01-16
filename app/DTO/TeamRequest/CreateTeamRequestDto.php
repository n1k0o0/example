<?php

namespace App\DTO\TeamRequest;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\DataTransferObject;

class CreateTeamRequestDto extends DataTransferObject
{
    #[MapFrom('team_id')]
    public int $league_request_id;

    //SomeCode

}
