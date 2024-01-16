<?php

namespace App\Http\Requests\Dashboard\LeagueRequest;

use App\Enums\LeagueRequest\LeagueRequestStatusEnum;
use App\Models\League;
use App\Models\LeagueRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * @mixin LeagueRequest
 */
class StoreLeagueRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return League::query()->findOrFail($this->league_id ?? 0)?->isNotStarted();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'color' => [
                'exclude_if:color,null',
                'nullable',
                'string',
                'min:1',
                'max:255',
            ],
            'league_id' => [
                'required',
                'integer',
                Rule::exists(League::class, 'id')
            ],
            'group_id' => ['nullable', 'required_if:status,2'],
            'school_id' => [
                'required',
                'integer',
                'min:1',
                Rule::unique(LeagueRequest::class, 'school_id')->where('color', $this->color ?? '')->where(
                    'league_id',
                    $this->league_id
                ),
            ],
            'status' => ['required', 'integer', new Enum(LeagueRequestStatusEnum::class)]
        ];
    }

    public function messages(): array
    {
        return ['school_id.unique' => 'Такая школа с таким цветом уже существует.'];
    }

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException("Для создании заявки турнир должен быть в статусе 'Текущий'");
    }
}
