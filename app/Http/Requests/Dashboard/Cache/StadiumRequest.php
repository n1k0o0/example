<?php

namespace App\Http\Requests\Dashboard\Cache;

use Illuminate\Foundation\Http\FormRequest;

/**
 *
 * @property int $id
 * @property bool $deleted
 *
*/
class StadiumRequest extends FormRequest
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
            'id' => ['required', 'integer'],
            'title' => ['required', 'string'],
            'address' => ['required', 'string'],
            'city_id' => ['required', 'integer'],
            'country_id' => ['required', 'integer'],
            'city' => ['nullable', 'array'],
            'country' => ['nullable', 'array'],
            'deleted' => ['nullable', 'boolean']
        ];
    }
}
