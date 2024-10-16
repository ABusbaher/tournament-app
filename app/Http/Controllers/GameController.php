<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAllGamesRequest;
use App\Http\Requests\UpdateGameScoreRequest;
use App\Models\Game;
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
            $data = $this->gameService->getGamesByFixture($tournament, $fixture);
            $games = $data['games'];
            $fixtures = $data['fixtures'];
            return view('games.byFixture', compact('fixtures', 'fixture', 'games'));
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

    /**
     * @throws AuthorizationException
     */
    public function store(CreateAllGamesRequest $request, Tournament $tournament): JsonResponse
    {
        $this->authorize('adminAccess', $request->user());
        $request->validated();
        $games = $this->gameService->createAllLeagueGames($tournament);
        return response()->json($games, 201);
    }

    public function show(Tournament $tournament, Game $game): JsonResponse
    {
        $gameByTournamentAndId = $this->gameService->getGame($tournament, $game);

        return response()->json($gameByTournamentAndId);
    }

    /**
     * @throws AuthorizationException
     */
    public function updateScore(UpdateGameScoreRequest $request, Tournament $tournament, Game $game): JsonResponse
    {
        $this->authorize('adminAccess', $request->user());
        $validatedData = $request->validated();
        try {
            $gameData = $this->gameService->updateGameScore($tournament, $game, $validatedData);
            return response()->json($gameData);
        } catch (NotFoundHttpException $exception) {
            return response()->json(['message' => 'Game not found for the given tournament and fixture.'], 404);
        }
    }

    public function showTable(Tournament $tournament): JsonResponse
    {
        try {
            $tableData = $this->gameService->getGameTable($tournament);
            return response()->json($tableData);
        } catch (NotFoundHttpException $exception) {
            return response()->json(['message' => 'Table not found for the given tournament.'], 404);
        }
    }
}
