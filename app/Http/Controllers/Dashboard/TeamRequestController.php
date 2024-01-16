<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\TeamRequest\CreateTeamRequest;
use App\Http\Requests\Dashboard\TeamRequest\IndexTeamRequest;
use App\Http\Resources\TeamRequest\TeamRequestResource;
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
     */
    public function store(CreateTeamRequest $request): JsonResponse
    {
        TeamRequest::query()->create($request->validated());
        return $this->respondEmpty();
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
}
