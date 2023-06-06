<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTournamentRequest;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(): View
    {
        return view('tournament.all', [
           'tournaments' => Tournament::paginate(10)
        ]);
    }

    public function store(CreateTournamentRequest $tournamentRequest): JsonResponse
    {
        $tournament = Tournament::create($tournamentRequest->validated());
        return response()->json(['tournament' => $tournament], 201);
    }
}
