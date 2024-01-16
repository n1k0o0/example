<?php

namespace App\Http\Controllers\User;

use App\Actions\Player\GetAvailablePlayersForTeamRequestFromCacheByLeagueRequestIdAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Player\GetBestPlayersRequest;
use App\Http\Requests\User\Player\GetAvailablePlayersForRequestRequest;
use App\Http\Resources\Player\PlayerCardResource;
use App\Http\Resources\Player\PlayerResource;
use App\Services\PlayerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PlayerController extends Controller
{

    /**
     * @param PlayerService $playerService
     */
    public function __construct(private readonly PlayerService $playerService)
    {
    }

    /**
     * @param GetBestPlayersRequest $request
     * @return AnonymousResourceCollection
     */
    public function getBestPlayers(GetBestPlayersRequest $request): AnonymousResourceCollection
    {
        $players = $this->playerService->getBestPlayers($request->validated(), $request->limit);
        return PlayerResource::collection($players);
    }

    /**
     * @param GetBestPlayersRequest $request
     * @return AnonymousResourceCollection
     */
    public function getPlayersOfMatch(GetBestPlayersRequest $request): AnonymousResourceCollection
    {
        $players = $this->playerService->getPlayersOfMatch($request->validated(), $request->limit);
        return PlayerResource::collection($players);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     * @throws UnknownProperties
     */
    public function getPlayerCard(int $id): JsonResponse
    {
        $playerCard = $this->playerService->getPlayerCard($id);
        return $this->respondSuccess(PlayerCardResource::make($playerCard));
    }

    public function getAvailablePlayersForRequest(GetAvailablePlayersForRequestRequest $request
    ): AnonymousResourceCollection {
        $availablePlayers = (new GetAvailablePlayersForTeamRequestFromCacheByLeagueRequestIdAction())->handle(
            $request->user()->school_id,
            $request->team_id,
            $request->search,
        );
        return PlayerResource::collection($availablePlayers);
    }

}
