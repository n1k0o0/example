<?php

namespace App\Http\Requests\Dashboard\Game;

use App\DTO\Game\UpdateGameDto;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UpdateGameRequest extends FormRequest
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
            'stadium_id'=>['nullable','integer','min:1'],
            'started_at'=>['nullable','date']
        ];
    }

    /**
     * @throws UnknownProperties
     */
    public function toDTO(): UpdateGameDto
    {
        return new UpdateGameDto($this->validated());
    }
}
