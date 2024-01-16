<?php

namespace App\Http\Requests\Dashboard\TeamRequest;

use App\Actions\Player\GetPlayerDataFromCacheByIdAction;
use App\Models\LeagueRequest;
use App\Models\TeamRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @mixin TeamRequest
 */
class CreateTeamRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'league_request_id' => ['required', 'integer', Rule::exists(LeagueRequest::class, 'id')],
            'player_id' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $player = (new GetPlayerDataFromCacheByIdAction())->handle($value);
                    if (empty($player) || $player['school_id'] !== LeagueRequest::findOrFail(
                            $this->league_request_id
                        )->school_id) {
                        $fail('Игрок не найден');
                    }
                },
                Rule::unique(TeamRequest::class, 'player_id')->where(
                    'league_request_id',
                    $this->league_request_id
                ),
            ],
            'position' => ['required', 'string', Rule::in(TeamRequest::POSITIONS)],
            'number' => ['nullable', 'integer', 'min:1', 'max:1000']
        ];
    }
}
