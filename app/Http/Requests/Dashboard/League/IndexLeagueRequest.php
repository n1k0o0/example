<?php

namespace App\Http\Requests\Dashboard\League;

use App\Enums\League\LeagueStatusEnum;
use App\Models\League;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * @mixin League
 * @property int $limit
 * @property int $school_id
 * @property array $statuses
 */
class IndexLeagueRequest extends FormRequest
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
            'status' => ['nullable', 'string', new Enum(LeagueStatusEnum::class)],
            'statuses' => ['nullable', 'array'],
            'statuses.*' => ['nullable', 'string', new Enum(LeagueStatusEnum::class)],
            'school_id' => ['nullable', 'integer', 'min:1']
        ];
    }
}
