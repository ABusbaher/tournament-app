<?php

namespace App\Http\Requests;

use App\Models\Team;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateAllLeagueFixturesRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $tournamentId = $this->input('tournament_id');
        $minTeams = 4;
        return [
            'tournament_id' => [
                'required',
                'exists:tournaments,id',
                function ($attribute, $value, $fail) use ($tournamentId, $minTeams) {
                    $teamCount = Team::where('tournament_id', $tournamentId)->count();
                    if ($teamCount < $minTeams) {
                        $fail("You need to have at least {$minTeams} created in order to run league fixtures creation.");
                    }
                }
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
