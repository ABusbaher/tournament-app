<?php

namespace App\Http\Requests;

use App\Models\Team;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $tournamentId = $this->input('tournament_id');
        $maxTeams = 32;
        return [
            'name' => 'required|string|min:3|max:255',
            'tournament_id' => [
                'required',
                'exists:tournaments,id',
                function ($attribute, $value, $fail) use ($tournamentId, $maxTeams) {
                    $teamCount = Team::where('tournament_id', $tournamentId)->count();
                    if ($teamCount >= $maxTeams) {
                        $fail('The maximum number of teams (' . $maxTeams . ') for this tournament has been reached.');
                    }
                }
            ],
            'image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
