<?php

namespace App\Repositories;

use App\Exceptions\BusinessLogicException;
use App\Models\ArchiveTable;
use App\Models\League;
use App\Models\ScoreTable;
use Illuminate\Database\Eloquent\Collection;

class ScoreTableRepository
{

    /**
     * @param $leagueId
     * @return Collection
     * @throws BusinessLogicException
     */
    public function getResultsTable($leagueId): Collection
    {
       // SomeCode

        throw new BusinessLogicException('Результаты для неначатых турниров неактивны');
    }

}
