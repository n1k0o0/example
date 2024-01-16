<?php

namespace App\Http\Controllers\Dashboard;

use App\Exceptions\BusinessLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Group\GetGroupsRequest;
use App\Http\Requests\Dashboard\League\CreateLeagueRequest;
use App\Http\Requests\Dashboard\League\IndexLeagueRequest;
use App\Http\Requests\Dashboard\League\UpdateLeagueRequest;
use App\Http\Requests\Dashboard\League\UpdateLeagueSettingsRequest;
use App\Http\Resources\Dashboard\League\LeagueResource;
use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\LeagueRequest\ResultsLeagueRequestResource;
use App\Http\Resources\ScoreTable\ScoreCardResource;
use App\Models\League;
use App\Services\LeagueService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class LeagueController extends Controller
{

    public function __construct(private readonly LeagueService $leagueService)
    {
    }


    /**
     * Display a listing of the resource.
     *
     * @param IndexLeagueRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexLeagueRequest $request): AnonymousResourceCollection
    {
        $leaguesQuery = League::query()
            ->with('avatar')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('id', 'desc');

        if ($request->limit) {
            return LeagueResource::collection($leaguesQuery->paginate($request->limit));
        }

        return LeagueResource::collection($leaguesQuery->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateLeagueRequest $request
     * @return JsonResponse
     * @throws UnknownProperties|Throwable|BusinessLogicException
     */
    public function store(CreateLeagueRequest $request): JsonResponse
    {
        $this->leagueService->create($request->toDTO());
        return $this->respondEmpty();
    }

    /**
     * Display the specified resource.
     *
     * @param League $league
     * @return LeagueResource
     */
    public function show(League $league): LeagueResource
    {
        return LeagueResource::make($league->loadMissing('avatar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLeagueRequest $request
     * @param League $league
     * @return JsonResponse
     * @throws UnknownProperties|BusinessLogicException|Throwable
     */
    public function update(UpdateLeagueRequest $request, League $league): JsonResponse
    {
        $this->leagueService->update($league, $request->toDTO());
        return $this->respondEmpty();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLeagueSettingsRequest $request
     * @param League $league
     * @return JsonResponse
     * @throws BusinessLogicException
     * @throws Throwable
     * @throws UnknownProperties
     */
    public function updateSettings(UpdateLeagueSettingsRequest $request, League $league): JsonResponse
    {
        $this->leagueService->updateSettings($league, $request->toDTO());
        return $this->respondEmpty();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param League $league
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(League $league): JsonResponse
    {
        $this->authorize('delete', $league);
        $league->delete();
        return $this->respondEmpty();
    }


    /**
     * @throws BusinessLogicException
     * @throws UnknownProperties
     */
    public function leagueCard(League $league): ScoreCardResource
    {
        $card = $this->leagueService->card($league);
        return ScoreCardResource::make($card);
    }


    /**
     * @throws UnknownProperties
     */
    public function groups(GetGroupsRequest $request): AnonymousResourceCollection
    {
        $groups = $this->leagueService->getGroups($request->toDTO());
        return GroupResource::collection($groups);
    }

    public function results(League $league): AnonymousResourceCollection
    {
        $results = $this->leagueService->getResults($league);
        return ResultsLeagueRequestResource::collection($results);
    }
}
