<?php

namespace App\Http\Requests\Dashboard\Game;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlayerOfTheMatchRequest extends FormRequest
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
            'player_id' => ['nullable', 'integer'],
            'team_id' => ['nullable', 'integer']
        ];
    }
}
