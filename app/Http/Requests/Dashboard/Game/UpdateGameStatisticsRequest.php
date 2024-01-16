<?php

namespace App\Http\Requests\Dashboard\Game;

use App\Enums\Game\GameStatusEnum;
use App\Models\Game;
use App\Models\GamePause;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * @property Game $game
 */
class UpdateGameStatisticsRequest extends FormRequest
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
            'status' => [
                'required',
                'string',
                new Enum(GameStatusEnum::class)
            ],
            'actual_start_time' => ['nullable', 'date'],
            'actual_finish_time' => ['nullable', 'date', 'after_or_equal:actual_start_time'],
            'removed_pause_ids' => ['nullable', 'array'],
            'removed_pause_ids.*' => ['nullable', 'integer', Rule::exists(GamePause::class, 'id')],
            'pauses' => ['nullable', 'array'],
            'pauses.*.id' => ['nullable', 'integer', Rule::exists(GamePause::class, 'id')],
            'pauses.*.started_at' => ['required', 'date'],
            'pauses.*.finished_at' => ['required', 'date', 'after_or_equal:pauses.*.started_at'],
        ];
    }

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException("Не все команды определены еще!");
    }
}
