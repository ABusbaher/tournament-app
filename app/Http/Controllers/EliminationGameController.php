<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAllLeagueFixturesRequest;
use App\Models\Tournament;
use App\Services\EliminationGameService;
use Illuminate\Http\JsonResponse;

class EliminationGameController extends Controller
{
    protected EliminationGameService $gameService;
    public function __construct(EliminationGameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function store(CreateAllLeagueFixturesRequest $request, Tournament $tournament): JsonResponse
    {
        $request->validated();
        $games = $this->gameService->createAllEliminationGames($tournament);
        return response()->json($games, 201);
    }
}
