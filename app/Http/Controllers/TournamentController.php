<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTournamentRequest;
use App\Http\Requests\UpdateTournamentNameRequest;
use App\Http\Requests\UpdateTournamentRequest;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(): View
    {
        return view('tournament.all');
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

    public function show(Tournament $tournament): JsonResponse
    {
        return response()->json($tournament);
    }

    public function updateName(UpdateTournamentNameRequest $tournamentRequest, Tournament $tournament): JsonResponse
    {
        $tournament->update($tournamentRequest->only('name'));
        return response()->json($tournament);
    }

    public function updateAll(UpdateTournamentRequest $tournamentRequest, Tournament $tournament): JsonResponse
    {
        $data = $tournamentRequest->validated();
        unset($data['tournament_id']); // Remove the tournament_id field
        $tournament->update($data);
        return response()->json($tournament);
    }

    public function destroy(Tournament $tournament): JsonResponse
    {
        $tournament->delete();
        return response()->json([
            'message' => 'Tournament deleted successfully',
        ], 204);
    }
}
