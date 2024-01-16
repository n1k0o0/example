<?php

namespace App\Http\Controllers\Dashboard;

use App\Exceptions\BusinessLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Game\AddAndRemoveGoalRequest;
use App\Http\Requests\Dashboard\Game\AddGoalJuryRequest;
use App\Http\Requests\Dashboard\Game\CreateGamesRequest;
use App\Http\Requests\Dashboard\Game\CreatePlayoffRequest;
use App\Http\Requests\Dashboard\Game\DeleteGoalJuryRequest;
use App\Http\Requests\Dashboard\Game\GetGamesRequest;
use App\Http\Requests\Dashboard\Game\UpdateGameRequest;
use App\Http\Requests\Dashboard\Game\UpdateGameStatisticsRequest;
use App\Http\Requests\Dashboard\Game\UpdateGameStatusRequest;
use App\Http\Requests\Dashboard\Game\UpdatePlayerOfTheMatchRequest;
use App\Http\Resources\Game\GameResource;
use App\Models\Game;
use App\Models\League;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class GameController extends Controller
{
    /**
     * @param GameService $gameService
     */
    public function __construct(private readonly GameService $gameService)
    {
        $this->middleware('auth.jwt:admin', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param GetGamesRequest $request
     * @return AnonymousResourceCollection
     * @throws UnknownProperties
     */
    public function index(GetGamesRequest $request): AnonymousResourceCollection
    {
        $games = $this->gameService->getGames(
            $request->toDTO(),
            $request->input('limit'),
            orderDirection: 'desc'
        );

        return GameResource::collection($games);
    }

    /**
     * @throws UnknownProperties
     */
    public function update(UpdateGameRequest $request, Game $game): JsonResponse
    {
        $this->gameService->update($request->toDTO(), $game);
        return $this->respondEmpty();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $game = $this->gameService->getGameById($id);

        return $this->respondSuccess(GameResource::make($game->loadMissing('playerOfTheMatch')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateGamesRequest $request
     * @return JsonResponse
     * @throws BusinessLogicException
     * @throws Throwable
     */
    public function store(CreateGamesRequest $request): JsonResponse
    {
        $this->authorize('createGame', League::findOrFail($request->games[0]['league_id']));

        $this->gameService->createGames($request->toDTO());

        return $this->respondEmpty();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Game $game
     * @return JsonResponse
     * @throws BusinessLogicException
     * @throws Throwable
     */
    public function destroy(Game $game): JsonResponse
    {
        $this->gameService->removeGame($game);

//        activityLog($game, 'Пользователь удалил игру', 'game');

        return $this->respondEmpty();
    }


    /**
     * Display a listing of the resource.
     *
     *
     * @param GetGamesRequest $request
     * @return AnonymousResourceCollection
     * @throws UnknownProperties
     */
    public function getSchedule(GetGamesRequest $request): AnonymousResourceCollection
    {
        $games = $this->gameService->getSchedule(
            $request->toDTO(),
            $request->input('limit'),
        );

        return GameResource::collection($games);
    }

    /**
     * Display a listing of the resource.
     *
     *
     * @param GetGamesRequest $request
     * @return AnonymousResourceCollection
     * @throws UnknownProperties
     */
    public function getResults(GetGamesRequest $request): AnonymousResourceCollection
    {
        $games = $this->gameService->getResults(
            $request->toDTO(),
            $request->input('limit'),
        );

        return GameResource::collection($games);
    }


    /**
     * Jury update game status
     * @param UpdateGameStatusRequest $request
     * @param Game $game
     * @return JsonResponse
     * @throws Throwable
     */
    public function updateStatus(UpdateGameStatusRequest $request, Game $game): JsonResponse
    {
        $this->gameService->updateStatus($request->validated(), $game->id);

//        activityLog($game, 'Пользователь изменил статус игры', 'game');

        return $this->respondEmpty();
    }

    /**
     * @param Game $game
     * @param UpdateGameStatisticsRequest $request
     * @return JsonResponse
     * @throws BusinessLogicException
     * @throws Throwable
     */
    public function updateStatistics(Game $game, UpdateGameStatisticsRequest $request): JsonResponse
    {
        $this->gameService->updateStatistics($game, $request->validated());

//        activityLog($game, 'Пользователь изменил статистику игры', 'game');

        return $this->respondEmpty();
    }


    /**
     * @throws BusinessLogicException
     */
    public function startPause(Game $game): JsonResponse
    {
        $this->gameService->startGamePause($game);

        return $this->respondEmpty();
    }

    /**
     * @throws BusinessLogicException
     */
    public function finishPause(Game $game): JsonResponse
    {
        $this->gameService->finishGamePause($game);

        return $this->respondEmpty();
    }


    /**
     * @param Game $game
     * @param AddAndRemoveGoalRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function addGoalAdmin(Game $game, AddAndRemoveGoalRequest $request): JsonResponse
    {
        $this->gameService->addGoalAdmin($game, $request->validated());

//        activityLog($game, 'Пользователь добавил гол', 'game');

        return $this->respondEmpty();
    }

    /**
     * @param Game $game
     * @param AddAndRemoveGoalRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function deleteGoalAdmin(Game $game, AddAndRemoveGoalRequest $request): JsonResponse
    {
        $this->gameService->deleteGoalAdmin($game, $request->validated());

//        activityLog($game, 'Пользователь удалил гол', 'game');

        return $this->respondEmpty();
    }

    /**
     * @param Game $game
     * @param AddGoalJuryRequest $request
     * @return JsonResponse
     * @throws BusinessLogicException
     */
    public function addGoalJury(Game $game, AddGoalJuryRequest $request): JsonResponse
    {
        $this->gameService->addGoalJury($game, $request->validated());

//        activityLog($game, 'Пользователь добавил гол', 'game');

        return $this->respondEmpty();
    }

    /**
     * @param Game $game
     * @param DeleteGoalJuryRequest $request
     * @return JsonResponse
     * @throws BusinessLogicException
     */
    public function deleteGoalJury(Game $game, DeleteGoalJuryRequest $request): JsonResponse
    {
        $this->gameService->deleteGoalJury($game, $request->validated());

//        activityLog($game, 'Пользователь удалил гол', 'game');

        return $this->respondEmpty();
    }


    /**
     * @param Game $game
     * @param UpdatePlayerOfTheMatchRequest $request
     * @return JsonResponse
     */
    public function updatePlayerOfTheMatch(Game $game, UpdatePlayerOfTheMatchRequest $request): JsonResponse
    {
        $this->gameService->updatePlayerOfTheMatch($game, $request->validated());
        return $this->respondEmpty();
    }


    /**
     * @throws Throwable
     */
    public function storePlayoff(CreatePlayoffRequest $request): JsonResponse
    {
        $this->gameService->storePlayoff($request->toDTO());
        return $this->respondEmpty();
    }
}
