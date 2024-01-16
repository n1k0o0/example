<?php

namespace App\Services;

use App\DTO\Game\GetGamesDto;
use App\Repositories\GameRepository;
use App\Repositories\PlayerRepository;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PlayerService
{

    /**
     * @param PlayerRepository $playerRepository
     * @param GameRepository $gameRepository
     */
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly GameRepository $gameRepository,
    ) {
    }

    /**
     * @param $data
     * @param int|null $limit
     * @return Collection
     */
    public function getBestPlayers($data, int $limit = null): Collection
    {
        return $this->playerRepository->getBestPlayers($data, $limit);
    }

    /**
     * @param $data
     * @param int|null $limit
     * @return Collection
     */
    public function getPlayersOfMatch($data, int $limit = null): Collection
    {
        return $this->playerRepository->getPlayersOfMatch($data, $limit);
    }

    /**
     * @param $playerId
     * @return object
     * @throws UnknownProperties
     */
    public function getPlayerCard($playerId): object
    {
        $playersOfMatch = $this->playerRepository->getPlayersOfMatch(
            ['player_id' => $playerId, 'current_tournament' => true]
        )->first();
        $bestPlayers = $this->playerRepository->getBestPlayers(['player_id' => $playerId, 'current_tournament' => true]
        )->first();
        $results = $this->gameRepository->getResults(
            new GetGamesDto(['player_id' => $playerId, 'current_tournament' => true])
        );

        $player['player_of_the_matches_count'] = data_get($playersOfMatch, 'player_of_the_matches_count', 0);
        $player['games'] = data_get($bestPlayers, 'games', 0);
        $player['goals'] = data_get($bestPlayers, 'goals', 0);
        return (object)[
            'player' => $player,
            'results' => $results
        ];
    }

}
