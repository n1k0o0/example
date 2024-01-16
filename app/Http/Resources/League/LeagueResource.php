<?php

namespace App\Http\Resources\League;

use App\Http\Resources\ImageResource;
use App\Http\Resources\LeagueRequest\LeagueRequestResource;
use App\Models\League;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @mixin League
 */
class LeagueResource extends JsonResource
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
            'groups' => $this->groups,
            'status' => $this->status,
            'city_name' => $this->city,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'avatar' => ImageResource::make($this->whenLoaded('avatar')),
            'league_requests' => LeagueRequestResource::collection($this->whenLoaded('leagueRequests')),
        ];
    }
}
