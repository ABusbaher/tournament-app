<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAllLeagueFixturesRequest;
use App\Models\Tournament;
use App\Services\EliminationGameService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

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

    public function index(Tournament $tournament): \Illuminate\Contracts\Foundation\Application|Factory|View|Application
    {
        try {
            $games = $this->gameService->getEliminationGames($tournament);
            return view('games.byElimination', compact( 'games'));
        } catch (NotFoundHttpException $exception) {
            abort(404);
        }
    }

    public function getByTournament(Tournament $tournament): JsonResponse
    {
        try {
            $games = $this->gameService->getEliminationGames($tournament);
            return response()->json($games);
        } catch (NotFoundHttpException $exception) {
            return response()->json(['message' => 'No elimination games found for the given tournament.'], 404);
        }
    }
}
