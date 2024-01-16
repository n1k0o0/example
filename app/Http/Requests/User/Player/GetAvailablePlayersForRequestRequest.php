<?php

namespace App\Http\Requests\User\Player;

use App\Models\LeagueRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $team_id
 * @property string $search
 */
class GetAvailablePlayersForRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'team_id' => [
                'nullable',
                'integer',
                Rule::exists(LeagueRequest::class, 'id')->where('school_id', $this->user()->school_id)
            ],
            'search' => ['nullable', 'string', 'max:255']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
