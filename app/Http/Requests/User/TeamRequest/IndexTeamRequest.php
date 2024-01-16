<?php

namespace App\Http\Requests\User\TeamRequest;

use App\DTO\TeamRequest\IndexTeamRequestDto;
use App\Enums\League\LeagueStatusEnum;
use App\Models\Group;
use App\Models\LeagueRequest;
use App\Models\TeamRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

/**
 * @mixin TeamRequest
 * @mixin LeagueRequest
 * @property int $limit
 */
class IndexTeamRequest extends FormRequest
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
            'team_id' => ['required', 'integer', Rule::exists(LeagueRequest::class, 'id')],
            'status' => ['nullable', new Enum(LeagueStatusEnum::class)],
            'group_id' => ['nullable', Rule::exists(Group::class, 'id')],
        ];
    }

    /**
     * @throws UnknownProperties
     */
    public function toDTO(): IndexTeamRequestDto
    {
        return new IndexTeamRequestDto([...$this->validated(), 'school_id' => $this->user()?->school_id ?? null]);
    }
}
