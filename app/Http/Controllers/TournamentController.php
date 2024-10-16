<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTournamentRequest;
use App\Http\Requests\UpdateTournamentNameRequest;
use App\Http\Requests\UpdateTournamentRequest;
use App\Models\EliminationGame;
use App\Models\Game;
use App\Models\Tournament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    /**
     * @throws AuthorizationException
     */
    public function store(CreateTournamentRequest $tournamentRequest): JsonResponse
    {
        $this->authorize('adminAccess', $tournamentRequest->user());
        $tournament = Tournament::create($tournamentRequest->validated());
        return response()->json(['tournament' => $tournament], 201);
    }

    public function show(Tournament $tournament): JsonResponse
    {
        return response()->json($tournament);
    }

    /**
     * @throws AuthorizationException
     */
    public function updateName(UpdateTournamentNameRequest $tournamentRequest, Tournament $tournament): JsonResponse
    {
        $this->authorize('adminAccess', $tournamentRequest->user());
        $tournament->update($tournamentRequest->only('name'));
        return response()->json($tournament);
    }

    /**
     * @throws AuthorizationException
     */
    public function updateAll(UpdateTournamentRequest $tournamentRequest, Tournament $tournament): JsonResponse
    {
        $this->authorize('adminAccess', $tournamentRequest->user());
        $data = $tournamentRequest->validated();
        unset($data['tournament_id']); // Remove the tournament_id field
        $tournament->update($data);
        return response()->json($tournament);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Request $request,Tournament $tournament): JsonResponse
    {
        $this->authorize('adminAccess', $request->user());
        $tournament->delete();
        return response()->json([
            'message' => 'Tournament deleted successfully',
        ], 204);
    }

    public function getGamesByTournaments(): array
    {
        $fixtureGames =   Game::join('tournaments', 'games.tournament_id', '=', 'tournaments.id')
            ->groupBy('games.tournament_id', 'tournaments.name')
            ->pluck('tournaments.name', 'games.tournament_id')
            ->toArray();

        $eliminationGames = EliminationGame::join('tournaments', 'elimination_games.tournament_id', '=', 'tournaments.id')
            ->groupBy('elimination_games.tournament_id', 'tournaments.name')
            ->pluck('tournaments.name', 'elimination_games.tournament_id')
            ->toArray();

        return [
            'fixtureGames' => $fixtureGames,
            'eliminationGames' => $eliminationGames,
        ];
    }
}
