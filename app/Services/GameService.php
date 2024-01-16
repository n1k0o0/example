<?php

namespace App\Services;

use App\DTO\Game\CreateGameDto;
use App\DTO\Game\CreateGamesArrayDto;
use App\DTO\Game\CreatePlayoffDto;
use App\DTO\Game\GetGamesDto;
use App\DTO\Game\UpdateGameDto;
use App\Enums\Game\GameStatusEnum;
use App\Exceptions\BusinessLogicException;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\PlayerOfTheMatch;
use App\Models\ScoreTable;
use App\Models\TeamRequest;
use App\Repositories\GameRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class GameService
{
    /**
     * @param GameRepository $gameRepository
     */
    public function __construct(
        private readonly GameRepository $gameRepository,
    ) {
    }

    /**
     * @param int $gameId
     *
     * @return array|Builder|Collection|Model
     */
    public function getGameById(int $gameId): array|Builder|Collection|Model
    {
        return $this->gameRepository->getGameById($gameId);
    }

    /**
     * @param GetGamesDto $data
     * @param int|null $limit
     * @param string $orderDirection
     * @return LengthAwarePaginator|Collection
     */
    public function getGames(
        GetGamesDto $data,
        int $limit = null,
        string $orderDirection = 'asc'
    ): Collection|LengthAwarePaginator {
        return $this->gameRepository->getGames($data, $limit, orderDirection: $orderDirection);
    }

    /**
     * @param CreateGameDto $data
     *
     * @return void
     * @throws BusinessLogicException
     * @throws Throwable
     */
    public function createGames(CreateGameDto $data): void
    {
        try {
            DB::beginTransaction();
            foreach ($data->games as $game) {
                $this->createGame($game);
            }
            DB::commit();
        } catch (Exception $exception) {
            DB::rollback();
            throw $exception;
        }
    }

    /**
     * @param CreateGamesArrayDto $data
     *
     * @return Game
     * @throws BusinessLogicException
     * @throws Throwable
     */
    public function createGame(CreateGamesArrayDto $data): Game
    {
        if (!ScoreTable::query()
            ->where('team_id', $data->team_1_id)
            ->when(
                $data->group_id,
                fn($q) => $q->where('group_id', $data->group_id),
                fn($q) => $q->where('league_id', $data->league_id)
            )
            ->exists()
        ) {
            throw new BusinessLogicException(
                "Первая команда не принадлежит указанному Лиге/Группе"
            );
        }
        if (!ScoreTable::query()
            ->where('team_id', $data->team_2_id)
            ->when(
                $data->group_id,
                fn($q) => $q->where('group_id', $data->group_id),
                fn($q) => $q->where('league_id', $data->league_id)
            )
            ->exists()) {
            throw new BusinessLogicException(
                "Вторая команда не принадлежит указанному Лиге/Группе"
            );
        }

        try {
            DB::beginTransaction();
            /** @var Game $game */
            $game = Game::query()->create($data->toArray());

            $requests = TeamRequest::query()->whereIn('league_request_id', [$data->team_1_id, $data->team_2_id])
                ->select('league_request_id as team_id', 'player_id', 'number', 'position')
                ->get()
                ->toArray();
            $requests[] = ['player_id' => null, 'team_id' => $data->team_1_id];
            $requests[] = ['player_id' => null, 'team_id' => $data->team_2_id];
            $game->players()->createMany($requests);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollback();
            throw $exception;
        }

//        activityLog($game, 'Пользователь добавил игру', 'game');

        return $game;
    }

    /**
     * @param Game $game
     * @throws Throwable
     */
    public function removeGame(Game $game): void
    {
        DB::transaction(function () use ($game) {
            if ($game->round) {
                throw new BusinessLogicException('Нельзя удалять игры Плейофф');
            }
            if (!$game->isNotStarted()) {
                $this->gameRepository->calculateScoreAfterRemoveGame($game);
            }
            $game->delete();
        });
    }


    /**
     * @param GetGamesDto $data
     * @param int|null $limit
     * @return Collection|array|LengthAwarePaginator
     */
    public function getSchedule(GetGamesDto $data, int $limit = null): Collection|array|LengthAwarePaginator
    {
        return $this->gameRepository->getSchedule($data, $limit);
    }

    /**
     * @param GetGamesDto $data
     * @param int|null $limit
     * @return Collection|array|LengthAwarePaginator
     */
    public function getResults(GetGamesDto $data, int $limit = null): Collection|array|LengthAwarePaginator
    {
        return $this->gameRepository->getResults($data, $limit);
    }

    public function update(UpdateGameDto $data, Game $game): void
    {
        $game->update($data->toArray());
    }

    /**
     * @param $data
     * @param $gameId
     * @throws Throwable
     */
    public function updateStatus($data, $gameId): void
    {
        $this->gameRepository->updateStatus($data, $gameId);
    }

    /**
     * @param Game $game
     * @param $data
     * @throws BusinessLogicException
     * @throws Throwable
     */
    public function updateStatistics(Game $game, $data): void
    {
       // SomeCode
    }


    /**
     * @param Game $game
     * @return void
     * @throws BusinessLogicException
     */
    public function startGamePause(Game $game): void
    {
        if ($game->isActivePause()) {
            throw new BusinessLogicException('Уже есть активная пауза');
        }
        $game->pauses()->create(['started_at' => now()]);
    }

    /**
     * @param Game $game
     * @return void
     * @throws BusinessLogicException
     */
    public function finishGamePause(Game $game): void
    {
        if (!$game->isActivePause()) {
            throw new BusinessLogicException('Пауза не была начата');
        }
        $pause = $game->pauses()->latest('id')->first();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        $pause?->update(['finished_at' => now(), 'duration' => now()->diffInSeconds($pause?->started_at)]);
    }


    /**
     * @param Game $game
     * @param $data
     * @throws Throwable
     */
    public function addGoalAdmin(Game $game, $data): void
    {
        if ($game->round && $game->isFinished()) {
            throw new BusinessLogicException('Нельзя добавлять голы для завершенных плейофф матчей');
        }

        //SOmeCode
    }

    /**
     * @param Game $game
     * @param $data
     * @throws Throwable
     */
    public function deleteGoalAdmin(Game $game, $data): void
    {
        //SomeCOde
    }

    /**
     * @param Game $game
     * @param $data
     * @throws BusinessLogicException
     */
    public function addGoalJury(Game $game, $data): void
    {
        if ($game->isFinished()) {
            throw new BusinessLogicException('Нельзя добавить гол для завершенной игры!');
        }
        if ($game->isNotStarted()) {
            throw new BusinessLogicException('Нельзя добавить гол для не начатой игры!');
        }
        if ($game->isActivePause()) {
            throw new BusinessLogicException('Нельзя добавить гол во время паузы!');
        }

       //Some COde
    }

    /**
     * @param Game $game
     * @param $data
     * @throws BusinessLogicException
     */
    public function deleteGoalJury(Game $game, $data): void
    {
        if ($game->isFinished()) {
            throw new BusinessLogicException('Нельзя удалить гол для завершенной игры!');
        }
      //SomeCode
    }

    /**
     * @param $addedGoalTeamId
     * @param $anotherTeamId
     * @param $gameId
     * @param $group_id
     */
    public function updateScoreOnAddedGoal($addedGoalTeamId, $anotherTeamId, $gameId, $group_id): void
    {
        //SomeCode

    }

    /**
     * @param $removedGoalTeamId
     * @param $anotherTeamId
     * @param $gameId
     * @param $group_id
     */
    public function updateScoreOnRemoveGoal($removedGoalTeamId, $anotherTeamId, $gameId, $group_id): void
    {
        //SomeCode

    }


    /**
     * @param Game $game
     * @param $data
     * @return void
     */
    public function updatePlayerOfTheMatch(Game $game, $data): void
    {
        //SomeCode

    }


    /**
     * @throws BusinessLogicException
     * @throws Throwable
     */
    public function storePlayoff(CreatePlayoffDto $data): void
    {
        try {
            DB::beginTransaction();

            $createdGamesId = [];
            $round = $data->games[0]->round;

            foreach ($data->games as $game) {
                $newGame = $this->createGame($game);
                $createdGamesId[] = $newGame->id;
            }

            if ($round > 1) {
                $this->createPlayoffGames($createdGamesId, $round);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            throw new $e();
        }
    }

    public function createPlayoffGames(array $createdGamesId, int $round): void
    {
        $newCreatedGamesId = [];
        $newRound = $round / 2;

        //SomeCode


        if ($round > 2) {
            $this->createPlayoffGames($newCreatedGamesId, $newRound);
        }
    }

}
