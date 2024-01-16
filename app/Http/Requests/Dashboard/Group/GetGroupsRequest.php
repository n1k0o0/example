<?php

namespace App\Http\Requests\Dashboard\Group;

use App\DTO\Group\IndexGroupsDto;
use App\Models\League;
use App\Models\LeagueRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

/**
 * @property int $league_id
 * @property int $league_request_id
 */
class GetGroupsRequest extends FormRequest
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
            'league_id' => ['nullable', 'integer', Rule::exists(League::class, 'id')],
            'league_request_id' => ['nullable', 'integer', Rule::exists(LeagueRequest::class, 'id')],
        ];
    }

    /**
     * @throws UnknownProperties
     */
    public function toDTO(): IndexGroupsDto
    {
        return new IndexGroupsDto($this->validated());
    }
}
