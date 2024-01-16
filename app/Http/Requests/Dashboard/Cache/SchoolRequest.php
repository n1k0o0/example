<?php

namespace App\Http\Requests\Dashboard\Cache;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $id
 * @property string $name
 * @property bool $deleted
 */
class SchoolRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'min:1'],
            'name' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],

            'city_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
            'email' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'branch_count' => ['nullable', 'integer'],
            'inst_link' => ['nullable', 'string'],
            'youtube_link' => ['nullable', 'string'],
            'vk_link' => ['nullable', 'string'],
            'diagram_link' => ['nullable', 'string'],
            'city' => ['nullable', 'array'],
            'user' => ['nullable', 'array'],
            'country' => ['nullable', 'array'],
            'teams' => ['nullable', 'array'],
            'coaches' => ['nullable', 'array'],
            'avatar' => ['nullable', 'array'],

            'deleted' => ['nullable', 'boolean']
        ];
    }
}
