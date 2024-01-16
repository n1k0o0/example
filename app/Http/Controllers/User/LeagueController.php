<?php

namespace App\Http\Controllers\User;

use App\Exceptions\BusinessLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Group\GetGroupsRequest;
use App\Http\Requests\Dashboard\League\IndexLeagueRequest;
use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\League\LeagueResource;
use App\Http\Resources\LeagueRequest\ResultsLeagueRequestResource;
use App\Http\Resources\ScoreTable\ScoreCardResource;
use App\Models\League;
use App\Services\LeagueService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

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
            ->when($request->statuses, fn($q) => $q->whereIn('status', $request->statuses))
            ->when(
                $request->school_id,
                fn($q) => $q->whereHas('leagueRequests', fn($q) => $q->where('school_id', $request->school_id))
            );
        if ($request->limit) {
            return LeagueResource::collection($leaguesQuery->paginate($request->limit));
        }
        return LeagueResource::collection($leaguesQuery->get());
    }


    /**
     * Display the specified resource.
     *
     * @param League $league
     * @return LeagueResource
     */
    public function show(League $league): LeagueResource
    {
        return LeagueResource::make(
            $league->loadMissing(
                ['avatar', 'leagueRequests' => fn($q) => $q->where('school_id', request()?->user()?->school_id ?? null)]
            )
        );
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
