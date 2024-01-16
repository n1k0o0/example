<?php

namespace App\Services;

use App\DTO\LeagueRequest\IndexLeagueRequestDto;
use App\DTO\LeagueRequest\UpdateLeagueRequestDto;
use App\Exceptions\BusinessLogicException;
use App\Models\LeagueRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LeagueRequestService
{
    public function get(
        IndexLeagueRequestDto $indexLeagueRequestDto,
        int $limit = null
    ): Collection|array|LengthAwarePaginator {
        $query = LeagueRequest::query()
            ->with('group', 'league.avatar', 'teamRequests')
            ->withCount('teamRequests')
            ->leftJoin('leagues', 'league_requests.league_id', '=', 'leagues.id')
            ->when(
                $indexLeagueRequestDto->status,
                fn($q) => $q->where('league_requests.status', $indexLeagueRequestDto->status)
            )
            ->when(
                $indexLeagueRequestDto->statuses,
                fn($q) => $q->whereIn('league_requests.status', $indexLeagueRequestDto->statuses)
            )
            ->when($indexLeagueRequestDto->group_id, fn($q) => $q->where('group_id', $indexLeagueRequestDto->group_id))
            ->when(
                $indexLeagueRequestDto->league_id,
                fn($q) => $q->where('league_id', $indexLeagueRequestDto->league_id)
            )
            ->when(
                !empty($indexLeagueRequestDto->league_statuses),
                fn($q) => $q->whereHas(
                    'league',
                    fn($q) => $q->whereIn('status', $indexLeagueRequestDto->league_statuses)
                )
            )
            ->when(
                $indexLeagueRequestDto->school_id,
                fn($q) => $q->where('school_id', $indexLeagueRequestDto->school_id)
            )
            ->when(
                $indexLeagueRequestDto->team_name,
                fn($q) => $q->where('team_name', 'like', '%' . $indexLeagueRequestDto->team_name . '%')
            )
            ->orderByRaw(
                'case 
                        when leagues.status =2 then 1
                        when leagues.status =1  then 2 
                        when leagues.status= 3  then 3 
                        else 4 end'
            )
            ->orderByDesc('created_at')
            ->orderBy('league_requests.id');

        if ($limit) {
            return $query->paginate($limit);
        }
        return $query->get();
    }

    /**
     * @throws BusinessLogicException
     */
    public function update(LeagueRequest $leagueRequest, UpdateLeagueRequestDto $data): void
    {
        if (!$leagueRequest->league->isNotStarted()) {
            throw new BusinessLogicException("Турнир начат/завершен - нельзя редактировать");
        }
        $leagueRequest->update($data->toArray());
    }
}
