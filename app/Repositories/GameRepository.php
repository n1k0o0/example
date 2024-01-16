<?php

namespace App\Repositories;

use App\DTO\Game\GetGamesDto;
use App\Enums\Game\GameStatusEnum;
use App\Enums\League\LeagueStatusEnum;
use App\Exceptions\BusinessLogicException;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\ScoreTable;
use App\Models\TeamRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class GameRepository
{
    /**
     * @param int $gameId
     *
     * @return Model|Builder|Builder[]|Collection
     * @noinspection PhpUnused
     */
    public function getGameById(int $gameId): Builder|array|Collection|Model
    {
        /** @var Game $game */
        return Game::query()
            ->with(
                [
                    'group',
                    'league',
                    'pauses',
                    'firstTeam',
                    'secondTeam',
                    'firstTeamPlayers',
                    'secondTeamPlayers'
                ]
            )
            ->addSelect(
                [
                    'team_1_goals' => GamePlayer::query()
                        ->whereColumn('team_id', 'games.team_1_id')
                        ->whereColumn('game_id', 'games.id')
                        ->selectRaw('SUM(JSON_LENGTH(goals))'),
                    'team_2_goals' => GamePlayer::query()
                        ->whereColumn('team_id', 'games.team_2_id')
                        ->whereColumn('game_id', 'games.id')
                        ->selectRaw('SUM(JSON_LENGTH(goals))')
                ]
            )
            ->findOrFail($gameId);
    }

    /**
     * @param GetGamesDto $data
     * @param int|null $limit
     * @param string $orderDirection
     * @param string $orderBy
     * @return Collection|LengthAwarePaginator
     */
    public function getGames(
        GetGamesDto $data,
        int $limit = null,
        string $orderDirection = 'asc',
        string $orderBy = 'started_at'
    ): Collection|LengthAwarePaginator {
        // Some functions
    }

    /**
     * @param GetGamesDto $data
     * @param int|null $limit
     * @return Collection|array|LengthAwarePaginator
     */
    public function getSchedule(GetGamesDto $data, int $limit = null): Collection|array|LengthAwarePaginator
    {
        $data->statuses = [GameStatusEnum::NOT_STARTED->value, GameStatusEnum::STARTED->value];
        return $this->getGames(data: $data, limit: $limit);
    }

    /**
     * @param GetGamesDto $data
     * @param int|null $limit
     * @return Collection|array|LengthAwarePaginator
     */
    public function getResults(GetGamesDto $data, int $limit = null): Collection|array|LengthAwarePaginator
    {
        $data->status = GameStatusEnum::FINISHED->value;
        return $this->getGames(data: $data, limit: $limit, orderDirection: 'desc');
    }

    /**
     * @param $data
     * @param int $gameId
     * @param null $dateTime
     * @throws Throwable
     */
    public function updateStatus($data, int $gameId, $dateTime = null): void
    {
        $game = Game::query()->where('status', '<>', GameStatusEnum::FINISHED->value)->findOrFail($gameId);
        $status = data_get($data, 'start') ? GameStatusEnum::STARTED->value : GameStatusEnum::FINISHED->value;

        DB::transaction(function () use ($data, $game, $status, $dateTime) {
            $game->update(['status' => $status]);

            if ($status === GameStatusEnum::FINISHED->value) {
                if (data_get($data, 'game_player')) {
                    $game->playerOfTheMatch()->create([
                        'player_id' => data_get($data, 'game_player'),
                        'team_id' => data_get($data, 'team_id'),
                    ]);
                }

                $game->update(['actual_finish_time' => $dateTime ?? now()]);

                $team_1_goals = $this->getTeamGoalCount($game->id, $game->team_1_id);

                $team_2_goals = $this->getTeamGoalCount($game->id, $game->team_2_id);

                if ($team_1_goals > $team_2_goals) {
                    $game->update(['winner_id' => $game->team_1_id, 'looser_id' => $game->team_2_id]);
                }
                if ($team_1_goals < $team_2_goals) {
                    $game->update(['winner_id' => $game->team_2_id, 'looser_id' => $game->team_1_id]);
                }


                if ($game->group_id) {
                    // Some code
                }

                if ($game->round && $team_1_goals === $team_2_goals) {
                    throw new BusinessLogicException('В плейоффах игры не могут завершаться ничьей');
                }

                if ($game->round > 1) {
                    $winnerGame = Game::query()
                       // Some condition
                        ->where(fn($q) => $q->where('game_1_id', $game->id)->orWhere('game_2_id', $game->id))
                        ->firstOrFail();

                    if ($winnerGame->game_1_id === $game->id) {
                        $winnerGameUpdate = [
                            'team_1_id' => $game->winner_id
                        ];
                    } else {
                        $winnerGameUpdate = [
                            'team_2_id' => $game->winner_id
                        ];
                    }
                    $winnerGame->update($winnerGameUpdate);

                    /****  Create Game Players - Requests for Winner team for new winner game  *****/
                    $requests = 'Some data';
                    $requests[] = ['player_id' => null, 'team_id' => $game->winner_id];
                    $winnerGame->players()->createMany($requests);

                    $looserGame = Game::query()
                        ->where('league_id', $game->league_id)
                        ->where('round', $game->round / 2)
                        ->where('place_from', $game->place_from + $game->round)
                        ->where('place_to', $game->place_to)
                        ->where(fn($q) => $q->where('game_1_id', $game->id)->orWhere('game_2_id', $game->id))
                        ->firstOrFail();

                    if ($looserGame->game_1_id === $game->id) {
                        $looserGameUpdate = [
                            'team_1_id' => $game->looser_id
                        ];
                    } else {
                        $looserGameUpdate = [
                            'team_2_id' => $game->looser_id
                        ];
                    }
                    $looserGame->update($looserGameUpdate);

                    /****  Create Game Players - Requests for Winner team for new winner game  *****/
                    $requests = TeamRequest::query()->where('league_request_id', $game->looser_id)
                        ->select('league_request_id as team_id', 'player_id', 'number', 'position')
                        ->get()
                        ->toArray();
                    $requests[] = ['player_id' => null, 'team_id' => $game->looser_id];
                    $looserGame->players()->createMany($requests);
                }
            } else {
                $game->update(['actual_start_time' => now()]);
            }
        });
    }

    /** @noinspection DuplicatedCode */
    public function calculateScoreAfterRemoveGame(Game $game): void
    {
        // Some code
    }

    /**
     * @param int $gameId
     * @param int $teamId
     * @return int
     */
    public function getTeamGoalCount(int $gameId, int $teamId): int
    {
        /** @noinspection UnknownColumnInspection */
        return (int)(GamePlayer::query()
            ->where('team_id', $teamId)
            ->where('game_id', $gameId)
            ->selectRaw('SUM(JSON_LENGTH(goals)) as sumGoals')
            ->pluck('sumGoals')[0] ?? 0);
    }

}
