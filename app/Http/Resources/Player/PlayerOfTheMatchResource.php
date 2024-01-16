<?php

namespace App\Http\Resources\Player;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property int $player_id
 * @property object $player
 * @property int $player_of_the_matches_count
 */
class PlayerOfTheMatchResource extends JsonResource
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
            'player_id' => $this->player_id,
            'player_of_the_matches_count' => $this->player_of_the_matches_count,
            'player' => $this->player
        ];
    }
}
