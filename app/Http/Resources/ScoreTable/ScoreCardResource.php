<?php

namespace App\Http\Resources\ScoreTable;

use App\Http\Resources\Game\GameResource;
use App\Http\Resources\LeagueRequest\ResultsLeagueRequestResource;
use App\Http\Resources\Player\PlayerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property array $schedule
 * @property array $results
 * @property array resultsGroup
 * @property array resultsPlayoff
 * @property array $bestPlayers
 * @property array $scoreTable
 * @property array $resultsTable
 * @property array $playersOfTheMatch
 */
class ScoreCardResource extends JsonResource
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
            'schedule' => GameResource::collection($this->schedule),
            'resultsGroup' => GameResource::collection($this->resultsGroup),
            'resultsPlayoff' => $this->resultsPlayoff,
            'resultsTable' => ResultsLeagueRequestResource::collection($this->resultsTable),
            'bestPlayers' => PlayerResource::collection($this->bestPlayers),
            'scoreTable' => $this->scoreTable,
            'playersOfTheMatch' => PlayerResource::collection($this->playersOfTheMatch),
        ];
    }
}
