<?php

namespace App\Http\Resources\Game;

use App\Actions\Player\GetPlayerDataFromCacheByIdAction;
use App\Models\GamePlayer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GamePlayer
 */
class GamePlayersResource extends JsonResource
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
            'team_id' => $this->team_id,
            'position' => $this->position,
            'number' => $this->number,
            'goals' => $this->goals,
            'player_id' => $this->player_id,
            'player' => (new GetPlayerDataFromCacheByIdAction())->handle($this->player_id)
        ];
    }
}
