<?php

namespace App\Http\Resources\Player;

use App\Http\Resources\Game\GameResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property array $results
 * @property array|object $player
*/
class PlayerCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'player'=>$this->player,
            'results'=>GameResource::collection($this->results),
        ];
    }
}
