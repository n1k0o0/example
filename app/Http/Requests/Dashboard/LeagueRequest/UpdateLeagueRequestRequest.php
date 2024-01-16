<?php

namespace App\Http\Requests\Dashboard\LeagueRequest;

use App\DTO\LeagueRequest\UpdateLeagueRequestDto;
use App\Enums\LeagueRequest\LeagueRequestStatusEnum;
use App\Models\Group;
use App\Models\LeagueRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

/**
 * @mixin LeagueRequest
 * @property LeagueRequest $league_request
 */
class UpdateLeagueRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->league_request->league->isNotStarted();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'integer', new Enum(LeagueRequestStatusEnum::class)],
            'group_id' => [
                Rule::requiredIf((int)$this->status === LeagueRequestStatusEnum::ACCEPTED->value),
                'integer',
                Rule::exists(Group::class, 'id')
            ],
        ];
    }

    /**
     * @throws UnknownProperties
     */
    public function toDTO(): UpdateLeagueRequestDto
    {
        return new UpdateLeagueRequestDto($this->validated());
    }

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException("Для обновлении заявки турнир должен быть в статусе 'Текущий'");
    }
}
