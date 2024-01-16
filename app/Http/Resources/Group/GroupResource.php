<?php

namespace App\Http\Resources\Group;

use App\Http\Resources\League\LeagueResource;
use App\Http\Resources\LeagueRequest\LeagueRequestResource;
use App\Models\Group;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @mixin Group
 */
class GroupResource extends JsonResource
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
            'name' => $this->name,
            'league_id' => $this->league_id,
            'league' => LeagueResource::make($this->whenLoaded('league')),
            'league_requests' => LeagueRequestResource::collection($this->whenLoaded('leagueRequests')),
        ];
    }
}
