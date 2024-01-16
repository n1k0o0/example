<?php

namespace App\Repositories;

use App\Enums\Game\GameStatusEnum;
use App\Models\Game;
use App\Models\League;

class LeagueRepository
{

    /**
     * @param League $league
     * @return array
     */
    public function getResults(League $league): array
    {
        /** @noinspection UnknownColumnInspection */
        $results = $league->leagueRequests()
            ///...some Conditions
            ->selectRaw('if(games.winner_id=league_requests.id,games.place_from,games.place_to) as place')
            ->orderBy('place')
            ->get();

        $last = Game::query()
            ->where('league_id', $league->id)
            ->max('place_to');


        $res = [];

        for ($i = 1; $i < $last + 1; $i++) {
            $flag = false;
            foreach ($results as $result) {
                if ($i === $result->place) {
                    $res[] = (object)$result;
                    $flag = true;
                }
            }
            if (!$flag) {
                $res[] = (object)[
                    'id' => $i,
                    'place' => $i,
                    'name' => '---',
                    'status' => null,
                    'group_id' => null,
                    'league_id' => null,
                    'color' => null,
                    'school_name' => '---',
                    'school_id' => null,
                    'created_at' => null,
                ];
            }
        }


        return $res;
    }

}
