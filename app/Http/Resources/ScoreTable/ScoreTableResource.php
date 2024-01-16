<?php

namespace App\Http\Resources\ScoreTable;

use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\LeagueRequest\LeagueRequestResource;
use App\Models\ScoreTable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ScoreTable
 */
class ScoreTableResource extends JsonResource
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
            'team_id' => $this->team_id,
            'team_name' => $this->team_name,
            'game_count' => $this->game_count,
            'win' => $this->win,
            'draw' => $this->draw,
            'defeat' => $this->defeat,
            'goals' => $this->goals,
            'missed_goals' => $this->missed_goals,
            'score' => $this->score,
            'group_id ' => $this->group_id,
            'group' => GroupResource::make($this->whenLoaded('group')),
            'team' => LeagueRequestResource::make($this->whenLoaded('leagueRequest')),
        ];
    }
}
