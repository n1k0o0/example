<?php

namespace App\Http\Requests\User\TeamRequest;

use App\Models\LeagueRequest;
use App\Models\TeamRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $player_id
 * @property int $team_id
 */
class DeleteRequestByPlayerAndTeamIdRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'team_id' => [
                'required',
                'integer',
                Rule::exists(LeagueRequest::class, 'id')->where('school_id', $this->user()->school_id)
            ],
            'player_id' => ['required', 'integer', Rule::exists(TeamRequest::class, 'player_id')->where('league_request_id', $this->team_id)]
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->school_id;
    }
}
