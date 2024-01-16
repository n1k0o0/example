<?php

namespace App\Http\Requests\Dashboard\LeagueRequest;

use App\DTO\LeagueRequest\IndexLeagueRequestDto;
use App\Enums\League\LeagueStatusEnum;
use App\Enums\LeagueRequest\LeagueRequestStatusEnum;
use App\Models\Group;
use App\Models\League;
use App\Models\LeagueRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

/**
 * @mixin LeagueRequest
 * @property int $limit
 */
class GetLeagueRequestsRequest extends FormRequest
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
            'status' => ['nullable', 'integer', new Enum(LeagueRequestStatusEnum::class)],
            'statuses' => ['nullable', 'array'],
            'statuses.*' => ['required', 'integer', new Enum(LeagueRequestStatusEnum::class)],
            'league_statuses' => ['nullable', 'array'],
            'league_statuses.*' => ['nullable', 'integer', new Enum(LeagueStatusEnum::class)],
            'school_id' => ['nullable', 'integer', 'min:1'],
            'league_id' => ['nullable', 'integer', Rule::exists(League::class, 'id')],
            'group_id' => ['nullable', 'integer', Rule::exists(Group::class, 'id')],
            'team_name' => ['nullable', 'string']
        ];
    }

    /**
     * @throws UnknownProperties
     */
    public function toDTO(): IndexLeagueRequestDto
    {
        $data = $this->validated();
        if (data_get($this->user(), 'school_id')) {
            $data = [...$data, 'school_id' => $this->user()?->school_id ?? null];
        }
        return new IndexLeagueRequestDto($data);
    }
}
