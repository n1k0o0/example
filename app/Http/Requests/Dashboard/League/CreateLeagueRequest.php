<?php

namespace App\Http\Requests\Dashboard\League;

use App\DTO\League\CreateLeagueDto;
use App\Models\League;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

/**
 * @mixin League
 */
class CreateLeagueRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique(League::class, 'name')],
            'groups' => ['nullable', 'integer', 'max:64'],
            'round' => ['nullable', 'integer', 'max:4'],
            'places' => ['nullable', 'integer', 'max:128'],
            'city' => ['required', 'string', 'max:256'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after:started_at'],
            'avatar_upload' => ['nullable', 'mimes:jpg,png,jpeg,svg', 'max:5120'],
        ];
    }

    /**
     * @throws UnknownProperties
     */
    public function toDTO(): CreateLeagueDto
    {
        return new CreateLeagueDto($this->validated());
    }
}
