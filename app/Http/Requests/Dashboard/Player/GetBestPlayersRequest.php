<?php

namespace App\Http\Requests\Dashboard\Player;

use App\Models\Group;
use App\Models\League;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $limit
 */
class GetBestPlayersRequest extends FormRequest
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
            'group_id' => ['nullable', 'integer', Rule::exists(Group::class, 'id')],
            'league_id' => ['nullable', 'integer', Rule::exists(League::class, 'id')],
        ];
    }
}
