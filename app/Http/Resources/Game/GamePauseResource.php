<?php

namespace App\Http\Resources\Game;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GamePauseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
