<?php

namespace App\Http\Resources\TeamRequest;

use App\Actions\Player\GetPlayerDataFromCacheByIdAction;
use App\Http\Resources\LeagueRequest\LeagueRequestResource;
use App\Models\TeamRequest;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @mixin TeamRequest
 */
class TeamRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'team_id' => $this->league_request_id,
            'player_id' => $this->player_id,
            'position' => $this->position,
            'number' => $this->number,
            'created_at' => $this->created_at,
            'league_request' => LeagueRequestResource::make($this->whenLoaded('league_request')),
            'player' => (new GetPlayerDataFromCacheByIdAction())->handle($this->player_id)
        ];
    }
}
