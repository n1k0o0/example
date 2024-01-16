<?php

namespace App\Http\Requests\Dashboard\Cache;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $id
 * @property string $name
 * @property bool $deleted
 */
class PlayerRequest extends FormRequest
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
            'id' => ['required', 'integer', 'min:1'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'patronymic' => ['nullable', 'string'],
            'birthday' => ['nullable', 'string'],
            'goals' => ['nullable', 'integer'],
            'games' => ['nullable', 'integer'],
            'school_id' => ['nullable', 'integer'],
            'school' => ['array', 'nullable'],
            'avatar' => ['array', 'nullable'],

            'deleted' => ['nullable', 'boolean']
        ];
    }
}
