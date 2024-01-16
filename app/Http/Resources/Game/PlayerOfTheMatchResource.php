<?php

namespace App\Http\Resources\Game;

use App\Actions\Player\GetPlayerDataFromCacheByIdAction;
use App\Models\GamePlayer;
use App\Models\PlayerOfTheMatch;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @mixin PlayerOfTheMatch
 * @property GamePlayer $gamePlayer
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
        $data[] = $this->whenLoaded('gamePlayer') ?
            [
                'id' => $this->player_id,
                'team_id' => $this->team_id,
                'position' => $this->gamePlayer->position,
                'number' => $this->gamePlayer->number
            ] : null;
        return [
            'team_id' => $this->team_id,
            'game_id' => $this->game_id,
            'player_id' => $this->player_id,
            'player' => (new GetPlayerDataFromCacheByIdAction())->handle(
                $this->player_id,
                $data
            )
        ];
    }
}
