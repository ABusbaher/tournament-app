<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAllLeagueFixturesRequest;
use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\GameService;
use Illuminate\Auth\Access\AuthorizationException;
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

    /**
     * @throws AuthorizationException
     */
    public function store(CreateAllLeagueFixturesRequest $request, Tournament $tournament): JsonResponse
    {
//        $this->authorize('create', $tournament);
        $request->validated();
        $games = $this->gameService->createAllLeagueGames($tournament);
        return response()->json($games, 201);
    }
}
