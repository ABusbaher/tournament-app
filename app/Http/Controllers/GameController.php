<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAllLeagueFixturesRequest;
use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\GameService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GameController extends Controller
{
    protected GameService $gameService;
    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function index(Tournament $tournament, $fixture): \Illuminate\Contracts\Foundation\Application|Factory|View|Application
    {
        try {
            $games = $this->gameService->getGamesByFixture($tournament, $fixture);
            $fixtures = $games->pluck('fixture')->unique();
            return view('games.byFixture', compact('fixtures'));
        } catch (NotFoundHttpException $exception) {
            abort(404);
        }
    }

    public function getByFixtures(Tournament $tournament, $fixture): JsonResponse
    {
        try {
            $games = $this->gameService->getGamesByFixture($tournament, $fixture);
            return response()->json($games);
        } catch (NotFoundHttpException $exception) {
            return response()->json(['message' => 'No games found for the given tournament and fixture.'], 404);
        }
    }

    public function store(CreateAllLeagueFixturesRequest $request, Tournament $tournament): JsonResponse
    {
//        $this->authorize('create', $tournament);
        $request->validated();
        $games = $this->gameService->createAllLeagueGames($tournament);
        return response()->json($games, 201);
    }
}
