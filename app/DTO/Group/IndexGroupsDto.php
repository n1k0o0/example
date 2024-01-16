<?php

namespace App\DTO\Group;

use Spatie\DataTransferObject\DataTransferObject;

class IndexGroupsDto extends DataTransferObject
{
    public ?int $league_request_id;

    public ?int $league_id;

}
