<?php

namespace App\Http\Requests\User\LeagueRequest;

use App\Models\League;
use App\Models\LeagueRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @mixin LeagueRequest
 * @property array|null $colors
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
            'colors' => ['nullable', 'array'],
            'colors.*' => [
                'distinct',
                'min:0',
                'max:255',
                Rule::unique(LeagueRequest::class, 'color')
                    ->where('league_id', $this->league_id)->where('school_id', $this->user()->school_id)
            ],
            'league_id' => [
                'required',
                'integer',
                Rule::exists(League::class, 'id'),

                function ($attribute, $value, $fail) {
                    if (in_array(null, $this->colors ?? ['', null], true) && LeagueRequest::query()->where(
                            'school_id',
                            $this->user()->school_id
                        )->where('league_id', $this->league_id)->where(
                            'color',
                            ''
                        )->exists()) {
                        $fail('Такая школа с таким цветом уже существует.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return ['league_id.unique' => 'Такая школа с таким цветом уже существует.'];
    }
}
