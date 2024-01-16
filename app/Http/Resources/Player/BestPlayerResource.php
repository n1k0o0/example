<?php

namespace App\Http\Resources\Player;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $player_id
 * @property int|null $sumGoals
 * @property int $games
 * @property object $player
 * @property int $player_of_the_matches_count
 */
class BestPlayerResource extends JsonResource
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
            'player_id' => $this->player_id,
            'goals' => (int)$this->sumGoals,
            'games' => $this->games ?? 0,
            'player' => $this->player,
        ];
    }
}
