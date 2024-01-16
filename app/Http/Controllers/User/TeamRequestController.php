<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\TeamRequest\CreateTeamRequest;
use App\Http\Requests\User\TeamRequest\DeleteRequestByPlayerAndTeamIdRequest;
use App\Http\Requests\User\TeamRequest\IndexTeamRequest;
use App\Http\Resources\TeamRequest\TeamRequestResource;
use App\Models\LeagueRequest;
use App\Models\TeamRequest;
use App\Services\TeamRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class TeamRequestController extends Controller
{

    public function __construct(private readonly TeamRequestService $teamRequestService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexTeamRequest $request
     * @return AnonymousResourceCollection
     * @throws UnknownProperties
     */
    public function index(IndexTeamRequest $request): AnonymousResourceCollection
    {
        $teamRequests = $this->teamRequestService->index($request->toDTO(), $request->limit);

        return TeamRequestResource::collection($teamRequests);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateTeamRequest $request
     * @return JsonResponse
     * @throws UnknownProperties
     */
    public function store(CreateTeamRequest $request): JsonResponse
    {
        TeamRequest::query()->create($request->toDTO()->toArray());
        return $this->respondEmpty();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param LeagueRequest $team
     * @return AnonymousResourceCollection
     */
    public function show(LeagueRequest $team): AnonymousResourceCollection
    {
        return TeamRequestResource::collection($team->teamRequests);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TeamRequest $teamRequest
     * @return JsonResponse
     */
    public function destroy(TeamRequest $teamRequest): JsonResponse
    {
        $teamRequest->delete();
        return $this->respondEmpty();
    }

    public function deleteRequestByPlayerAndTeamId(DeleteRequestByPlayerAndTeamIdRequest $request): JsonResponse
    {
        TeamRequest::query()
            ->whereHas(
                'leagueRequest',
                fn($q) => $q->where('id', $request->team_id)->where('school_id', $request->user()->school_id)
            )
            ->where('player_id', $request->player_id)
            ->delete();
        return $this->respondEmpty();
    }
}
