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
           'tournaments' => Tournament::orderBy('created_at', 'desc')->paginate(5)
        ]);
    }

    public function getAll(): JsonResponse
    {
        $tournaments = Tournament::orderBy('created_at', 'desc')->paginate(5);
        return response()->json(['tournaments' => $tournaments]);
    }

    public function store(CreateTournamentRequest $tournamentRequest): JsonResponse
    {
        $tournament = Tournament::create($tournamentRequest->validated());
        return response()->json(['tournament' => $tournament], 201);
    }
}
