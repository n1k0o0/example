<?php

namespace App\Http\Requests\Dashboard\Game;


use App\DTO\Game\GetGamesDto;
use App\Enums\Game\GameStatusEnum;
use App\Models\Group;
use App\Models\League;
use App\Models\LeagueRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class GetGamesRequest extends FormRequest
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
            'limit' => ['nullable', 'integer', 'max:250'],
            'page' => ['nullable', 'integer', 'min:1'],

            'team_id' => ['nullable', 'integer', Rule::exists(LeagueRequest::class, 'id')],

            'league_ids' => ['nullable', 'array'],
            'league_ids.*' => ['nullable', 'integer', Rule::exists(League::class, 'id')],
            'league_id' => ['nullable', 'integer', Rule::exists(League::class, 'id')],

            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => ['nullable', 'integer', Rule::exists(Group::class, 'id')],
            'group_id' => ['nullable', 'integer', Rule::exists(Group::class, 'id')],


            'stadium_ids' => ['nullable', 'array'],
            'stadium_ids.*' => ['nullable', 'integer'],
            'stadium_id' => ['nullable', 'integer'],

            'player_ids' => ['nullable', 'array'],
            'player_ids.*' => ['nullable', 'integer'],
            'player_id' => ['nullable', 'integer'],

            'statuses' => ['nullable', 'array'],
            'statuses.*' => ['nullable', 'string', new Enum(GameStatusEnum::class)],
            'status' => ['nullable', 'string', new Enum(GameStatusEnum::class)],

            'date' => ['nullable', 'date'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'name' => ['nullable', 'string', 'max:100'],
            'playoff' => ['nullable', 'integer', Rule::in([0, 1, 2])],
        ];
    }

    public function attributes(): array
    {
        return [
            'limit' => 'Лимит',
            'team_1_id' => 'Команда 1',
            'page' => 'Страница',
            'team_2_id' => 'Команда 2',
            'league_id' => 'Лига',
            'division_id' => 'Дивизион',
            'tournament_id' => 'Турнир',
            'stadium_id' => 'Стадион',
            'started_at' => 'Дата и время начала',
            'player_id' => 'Игрок',
            'date_from' => 'Дата начала матча',
            'date_to' => 'Дата завершения матча',
        ];
    }

    /**
     * @throws UnknownProperties
     */
    public function toDTO(): GetGamesDto
    {
        return new GetGamesDto($this->validated());
    }
}
