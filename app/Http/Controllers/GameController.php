<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Tournament;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{
    protected GameService $gameService;
    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function index()
    {

    }

    public function store(Tournament $tournament): JsonResponse
    {
        $games = $this->gameService->createAllLeagueGames($tournament);
        return response()->json($games, 201);
    }
}
