<?php

namespace App\Http\Controllers\User;

use App\Actions\School\GetSchoolDataFromCacheByIdAction;
use App\Exceptions\BusinessLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LeagueRequest\GetLeagueRequestsRequest;
use App\Http\Requests\Dashboard\LeagueRequest\UpdateLeagueRequestRequest;
use App\Http\Requests\User\LeagueRequest\StoreLeagueRequestRequest;
use App\Http\Resources\LeagueRequest\LeagueRequestResource;
use App\Http\Resources\ScoreTable\ScoreTableResource;
use App\Models\League;
use App\Models\LeagueRequest;
use App\Models\ScoreTable;
use App\Services\LeagueRequestService;
use Illuminate\Auth\Access\AuthorizationException;
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
        return LeagueRequestResource::make($leagueRequest->loadMissing('group', 'teamRequests'));
    }

    /**
     * @throws AuthorizationException
     */
    public function store(StoreLeagueRequestRequest $request): JsonResponse
    {
        $this->authorize('createRequest', League::findOrFail($request->league_id));

        if ($request->colors && count($request->colors) > 1) {
            foreach (collect($request->colors)->unique() as $color) {
                LeagueRequest::query()->create([
                    'league_id' => $request->league_id,
                    'color' => $color ?? '',
                    'school_id' => $request->user()->school_id,
                    'school_name' => (new GetSchoolDataFromCacheByIdAction())->handle(
                        $request->user()->school_id
                    )['name'],
                ]);
            }
        } else {
            LeagueRequest::query()->create([
                'league_id' => $request->league_id,
                'color' => $request->colors[0] ?? '',
                'school_id' => $request->user()->school_id,
                'school_name' => (new GetSchoolDataFromCacheByIdAction())->handle($request->user()->school_id)['name'],
            ]);
        }


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

    public function getResultTable(LeagueRequest $leagueRequest): AnonymousResourceCollection|JsonResponse
    {
        $scoreTable = ScoreTable::query()
            ->with('group', 'league', 'leagueRequest')
            ->where('group_id', $leagueRequest->group?->id)
            ->orderBy('score', 'desc')
            ->orderByRaw('(convert(goals, SIGNED )  - convert(missed_goals, SIGNED ) ) DESC')
            ->orderBy('id')
            ->get();

        return ScoreTableResource::collection($scoreTable);
    }

}
