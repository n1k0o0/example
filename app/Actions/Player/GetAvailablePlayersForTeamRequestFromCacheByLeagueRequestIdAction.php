<?php

namespace App\Actions\Player;

use App\Models\LeagueRequest;
use Illuminate\Support\Collection;

class GetAvailablePlayersForTeamRequestFromCacheByLeagueRequestIdAction
{
    public function handle(
        int $school_id,
        int $id = null,
        string $search = null,
    ): Collection|array|null {
        $leagueRequest = LeagueRequest::find($id);
        $requestIds = $leagueRequest?->teamRequests()?->pluck('player_id');

        $players = (new GetPlayersAction())->handle(
            'GetAvailablePlayersForTeamRequestFromCacheByLeagueRequestIdAction'
        );

        $players = $players->where('school_id', $school_id)->whereNotIn('id', $requestIds);

        if ($search) {
            return $players->filter(function ($player) use ($search) {
                return stripos(mb_strtolower($player['first_name']), mb_strtolower($search)) !== false || stripos(
                        mb_strtolower($player['last_name']),
                        mb_strtolower($search)
                    ) !== false;
            })->all();
        }

        return $players->all();
    }
}
