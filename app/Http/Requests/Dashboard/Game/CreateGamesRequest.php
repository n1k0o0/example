<?php

namespace App\Http\Requests\Dashboard\Game;

use App\DTO\Game\CreateGameDto;
use App\Models\Game;
use App\Models\Group;
use App\Models\League;
use App\Models\LeagueRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

/**
 * @mixin Game
 * @property array $games
 */
class CreateGamesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return League::query()->findOrFail($this->games[0]['league_id'] ?? 0)?->isCurrent();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'games.*.stadium_id' => ['required', 'integer'],
            'games.*.league_request_1_id' => ['required', 'integer', Rule::exists(LeagueRequest::class, 'id')],
            'games.*.league_request_2_id' => ['required', 'integer', Rule::exists(LeagueRequest::class, 'id')],
            'games.*.league_id' => ['required', 'integer', Rule::exists(League::class, 'id')],
            'games.*.group_id' => ['required', 'integer', Rule::exists(Group::class, 'id')],
            'games.*.started_at' => ['required', 'date'],
            'games.*.finished_at' => ['nullable', 'date'],
        ];
    }

    public function prepareForValidation(): void
    {
        $data = $this->toArray();
        data_fill($data, 'games.*.stadium_id', $this->stadium_id);
        $this->merge($data);
    }

    public function attributes(): array
    {
        return [
            'games.*.team_1_id' => 'Команда 1',
            'games.*.team_2_id' => 'Команда 2',
            'games.*.league_id' => 'Лига',
            'games.*.group_id' => 'Группа',
            'stadium_id' => 'Стадион',
            'games.*.started_at' => 'Дата и время начала игры',
            'games.*.finished_at' => 'Дата и время завершения игры',
        ];
    }

    /**
     * @throws UnknownProperties
     */
    public function toDTO(): CreateGameDto
    {
        return new CreateGameDto($this->validated());
    }

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException("Для создании игры турнир должен быть в статусе 'Текущий'");
    }
}
