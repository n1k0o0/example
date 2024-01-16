<?php

namespace App\Http\Resources\Game;

use App\Actions\Stadium\GetStadiumDataFromCacheByIdAction;
use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\League\LeagueResource;
use App\Http\Resources\LeagueRequest\LeagueRequestResource;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Game
 */
class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'team_1_id' => $this->team_1_id,
            'team_2_id' => $this->team_2_id,
            'group_id' => $this->group_id,
            'round' => $this->round,
            'start_place' => $this->start_place,
            'place_from' => $this->place_from,
            'place_to' => $this->place_to,
            'league_id' => $this->league_id,
            'league' => LeagueResource::make($this->whenLoaded('league')),
            'status' => $this->status,
            'team_1' => LeagueRequestResource::make($this->whenLoaded('firstTeam')),
            'team_2' => LeagueRequestResource::make($this->whenLoaded('secondTeam')),
            'group' => GroupResource::make($this->whenLoaded('group')),
            'stadium_id' => $this->stadium_id,
            'stadium' => (new GetStadiumDataFromCacheByIdAction($request->bearerToken()))->handle($this->stadium_id),
            'pauses' => GamePauseResource::collection($this->whenLoaded('pauses')),
            'team_1_players' => GamePlayersResource::collection($this->whenLoaded('firstTeamPlayers')),
            'team_2_players' => GamePlayersResource::collection($this->whenLoaded('secondTeamPlayers')),
            'player_of_the_match' => PlayerOfTheMatchResource::make($this->whenLoaded('playerOfTheMatch')),
            'started_at' => $this->started_at,
            'actual_start_time' => $this->actual_start_time,
            'actual_finish_time' => $this->actual_finish_time,
            'game_1_id' => $this->game_1_id,
            'game_2_id' => $this->game_2_id,
            'team_1_goals' => (string)((data_get($this, 'team_1_goals', '0')) ?? 0),
            'team_2_goals' => (string)((data_get($this, 'team_2_goals', '0')) ?? 0),
            'isChampionsLeague' => true
        ];
    }
}
