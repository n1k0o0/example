<?php

namespace App\Http\Resources\LeagueRequest;

use App\Actions\School\GetSchoolDataFromCacheByIdAction;
use App\Models\LeagueRequest;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @mixin LeagueRequest
 * @property int $team_requests_count
 * @property int $place
 */
class ResultsLeagueRequestResource extends JsonResource
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
            'school_id' => $this->school_id,
            'school_name' => $this->school_name,
            'name' => $this->school_name . ' ' . $this->color,
            'color' => $this->color,
            'league_id' => $this->league_id,
            'group_id' => $this->group_id,
            'place' => $this->place,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'school' =>$this->school_id? (new GetSchoolDataFromCacheByIdAction())->handle($this->school_id):null,

        ];
    }
}
