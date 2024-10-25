<?php

namespace App\Http\Requests;

use App\Rules\DifferentIfNotNull;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEliminationGameScoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'team1_goals' => [
                'nullable', 'required_unless:team2_goals,null', 'integer', 'min:0', 'max:100',
                new DifferentIfNotNull('team2_goals'),
            ],
            'team2_goals' => ['nullable', 'required_unless:team1_goals,null', 'integer', 'min:0', 'max:100'],
            'game_time' => ['required', 'date'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
