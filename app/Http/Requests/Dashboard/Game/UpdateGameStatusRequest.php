<?php

namespace App\Http\Requests\Dashboard\Game;

use App\Models\Game;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Game $game
 */
class UpdateGameStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->game->team_1_id && $this->game->team_2_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'start' => ['required_without:finish', 'boolean', 'different:finish'],
            'finish' => ['required_without:start', 'boolean', 'different:start'],
            'game_player' => ['nullable', 'integer'],
            'team_id' => ['required_with:game_player', 'integer']
        ];
    }

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException("Не все команды определены еще!");
    }
}
