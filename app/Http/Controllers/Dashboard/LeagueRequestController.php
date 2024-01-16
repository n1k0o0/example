<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\Player\GetPlayersAction;
use App\Actions\School\GetSchoolDataFromCacheByIdAction;
use App\Exceptions\BusinessLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LeagueRequest\GetLeagueRequestsRequest;
use App\Http\Requests\Dashboard\LeagueRequest\StoreLeagueRequestRequest;
use App\Http\Requests\Dashboard\LeagueRequest\UpdateLeagueRequestRequest;
use App\Http\Resources\Dashboard\LeagueRequest\LeagueRequestResource;
use App\Http\Resources\Player\PlayerResource;
use App\Models\LeagueRequest;
use App\Services\LeagueRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class LeagueRequestController extends Controller
{
    public function __construct(private readonly LeagueRequestService $leagueRequestService)
    {
    }

    /**
     * @throws UnknownProperties
     */
    public function index(GetLeagueRequestsRequest $request): AnonymousResourceCollection
    {
        $leagueRequests = $this->leagueRequestService->get($request->toDTO(), $request->limit);
        return LeagueRequestResource::collection($leagueRequests);
    }

    public function show(LeagueRequest $leagueRequest): LeagueRequestResource
    {
        return LeagueRequestResource::make($leagueRequest->loadMissing('group'));
    }

    public function store(StoreLeagueRequestRequest $request): JsonResponse
    {
        LeagueRequest::query()->create([
            ...$request->validated(),
            'school_name' => (new GetSchoolDataFromCacheByIdAction())->handle($request->school_id)['name'],
        ]);

        return $this->respondEmpty();
    }

    /**
     * @throws UnknownProperties|BusinessLogicException
     */
    public function update(UpdateLeagueRequestRequest $request, LeagueRequest $leagueRequest): JsonResponse
    {
        $this->leagueRequestService->update($leagueRequest, $request->toDTO());
        return $this->respondEmpty();
    }

    public function destroy(LeagueRequest $leagueRequest): JsonResponse
    {
        $leagueRequest->delete();
        return $this->respondEmpty();
    }

    public function availablePlayers(LeagueRequest $leagueRequest): AnonymousResourceCollection
    {
        $players = (new GetPlayersAction())->handle('availablePlayers')
            ?->where('school_id', $leagueRequest->school_id)
            ->whereNotIn('id', $leagueRequest->teamRequests()->pluck('player_id'))
            ->all();

        return PlayerResource::collection($players);
    }
}
