<?php

namespace App\Http\Requests\Dashboard\League;

use App\DTO\League\UpdateLeagueDto;
use App\Enums\League\LeagueStatusEnum;
use App\Models\League;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

/**
 * @property League $league
 */
class UpdateLeagueRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(League::class, 'name')->ignoreModel($this->league)
            ],
            'city' => ['required', 'string', 'max:256'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after:started_at'],
            'status' => ['required', 'integer', new Enum(LeagueStatusEnum::class)],
            'avatar_upload' => ['nullable', 'mimes:jpg,png,jpeg,svg', 'max:5120'],
        ];
    }

    /**
     * @throws UnknownProperties
     */
    public function toDTO(): UpdateLeagueDto
    {
        return new UpdateLeagueDto($this->validated());
    }
}
