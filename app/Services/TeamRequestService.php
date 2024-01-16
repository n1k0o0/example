<?php

namespace App\Services;

use App\Actions\Player\GetPlayersAction;
use App\DTO\Dashboard\TeamRequest\IndexTeamRequestDto as DashboardIndexTeamRequestDto;
use App\DTO\TeamRequest\IndexTeamRequestDto;
use App\Models\TeamRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TeamRequestService
{
    public function index(
        IndexTeamRequestDto|DashboardIndexTeamRequestDto $data,
        int $limit = null
    ): Collection|array|LengthAwarePaginator {
        $query = TeamRequest::query()
            ->with('leagueRequest')
            ->when($data->league_request_id, fn($q) => $q->where('league_request_id', $data->league_request_id))
            ->when(
                $data->status,
                fn($q) => $q->whereHas('leagueRequest', fn($q) => $q->where('status', $data->status))
            )
            ->when(
                $data->group_id,
                fn($q) => $q->whereHas('leagueRequest', fn($q) => $q->where('group_id', $data->group_id))
            );

        if ($data->school_id) {
            $players = (new GetPlayersAction())->handle('TeamRequestService');
            $ids = $players->where('school_id', $data->school_id)->pluck('id');

            $query->whereIn('player_id', $ids);
        }

        if ($limit) {
            return $query->paginate($limit);
        }
        return $query->get();
    }
}
