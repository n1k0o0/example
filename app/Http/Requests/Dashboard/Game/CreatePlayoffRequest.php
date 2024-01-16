<?php

namespace App\Http\Requests\Dashboard\Game;

use App\DTO\Game\CreatePlayoffDto;
use App\Models\Game;
use App\Models\League;
use App\Models\LeagueRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

/**
 * @mixin Game
 * @property string $place
 */
class CreatePlayoffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return League::query()->findOrFail($this[0]['league_id'] ?? 0)?->isCurrent();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            '*.round' => ['required', 'integer', Rule::in([1, 2, 4, 8, 16])],
            '*.place_from' => [
                'required',
                'integer',
                'min:1',
                Rule::unique(Game::class, 'place_from')->where('league_id', $this[0]['league_id'])->where(
                    'place_to',
                    $this[0]['place_to']
                )
            ],
            '*.place_to' => ['required', 'integer', 'min:1', 'gte:*.place_from'],
            '*.start_place' => ['required', 'string', 'max:128'],
            '*.stadium_id' => ['required', 'integer'],
            '*.league_request_1_id' => ['nullable', 'integer', Rule::exists(LeagueRequest::class, 'id')],
            '*.league_request_2_id' => ['nullable', 'integer', Rule::exists(LeagueRequest::class, 'id')],
            '*.league_id' => ['required', 'integer', Rule::exists(League::class, 'id')],
            '*.started_at' => ['required', 'date'],
            '*.finished_at' => ['nullable', 'date'],
        ];
    }

    public function prepareForValidation(): void
    {
        $data = $this->toArray();
        data_fill($data, '*.start_place', $this[0]['place_from'] . '-' . $this[0]['place_to']);
        $this->merge($data);
    }

    /**
     * @throws UnknownProperties
     */
    public function toDTO(): CreatePlayoffDto
    {
        return new CreatePlayoffDto(['games' => $this->validated()]);
    }

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException("Для создании игры турнир должен быть в статусе 'Текущий'");
    }
}
