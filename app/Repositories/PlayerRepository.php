<?php

namespace App\Repositories;

use App\Actions\Player\GetPlayersAction;
use App\Enums\League\LeagueStatusEnum;
use App\Models\GamePlayer;
use App\Models\PlayerOfTheMatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PlayerRepository
{

    /**
     * @param $data
     * @param null $limit
     * @return Collection
     */
    public function getBestPlayers($data, $limit = null): Collection
    {
        $gamePlayers = $someData
            ->get();


        $players = (new GetPlayersAction())->handle('TeamRequestService')?->whereIn(
            'id',
            $gamePlayers->pluck('player_id')
        )->all();


        foreach ($players as &$player) {
            $player['goals'] = (int)$gamePlayers->firstWhere('player_id', $player['id'])->sumGoals;
            $player['games'] = (int)$gamePlayers->firstWhere('player_id', $player['id'])->games;
            $player['player_of_the_matches_count'] = null;
        }

        return collect($players)->sortByDesc('goals');
    }


    /**
     * @param array $data
     * @param null $limit
     * @return Collection
     */
    public function getPlayersOfMatch(array $data, $limit = null): Collection
    {
        $playersOfTheMatch = $someData
            ->get();

        $players = (new GetPlayersAction())->handle('getPlayersOfMatch')?->whereIn(
            'id',
            $playersOfTheMatch->pluck('player_id')
        )->all();


        foreach ($players as &$player) {
            $player['player_of_the_matches_count'] = (int)$playersOfTheMatch->firstWhere(
                'player_id',
                $player['id']
            )->player_of_the_matches_count;
            $player['games'] = null;
            $player['goals'] = null;
        }


        return collect($players)->sortByDesc('player_of_the_matches_count');
    }
}
