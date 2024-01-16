<?php

namespace App\Http\Requests\Dashboard\League;

use App\DTO\League\UpdateLeagueSettingsDto;
use App\Models\League;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

/**
 * @property League $league
 */
class UpdateLeagueSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->league->isNotStarted();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'groups' => [
                'nullable',
                'integer',
                'min:1'
            ]
        ];
    }

    /**
     * @throws UnknownProperties
     */
    public function toDTO(): UpdateLeagueSettingsDto
    {
        return new UpdateLeagueSettingsDto($this->validated());
    }

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException("Для редактирования настройки турнир должен быть в статусе 'Текущий'");
    }
}
