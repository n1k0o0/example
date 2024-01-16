<?php

namespace App\Http\Requests\Dashboard\Game;

use App\Models\GamePlayer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddGoalJuryRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'game_player_id' => ['required', 'integer', Rule::exists(GamePlayer::class, 'id')]
        ];
    }
}
