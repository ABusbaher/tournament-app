<?php

namespace App\Http\Requests;

use App\Models\Game;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateGameScoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'host_goals' => ['nullable', 'required_unless:guest_goals,null', 'integer', 'min:0', 'max:100'],
            'guest_goals' => ['nullable', 'required_unless:host_goals,null', 'integer', 'min:0', 'max:100'],
            'game_time' => ['required', 'date'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
