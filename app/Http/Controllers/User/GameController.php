<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Game\GetGamesRequest;
use App\Http\Resources\Game\GameResource;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

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
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $game = $this->gameService->getGameById($id);

        return $this->respondSuccess(
            GameResource::make(
                $game->loadMissing(['playerOfTheMatch.gamePlayer' => fn($q) => $q->where('game_id', $game->id)])
            )
        );
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

}
