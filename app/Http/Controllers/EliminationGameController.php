<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAllGamesRequest;
use App\Http\Requests\UpdateEliminationGameScoreRequest;
use App\Models\EliminationGame;
use App\Models\Tournament;
use App\Services\EliminationGameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Throwable;

class EliminationGameController extends Controller
{
    protected EliminationGameService $gameService;
    public function __construct(EliminationGameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * @throws Throwable
     */
    public function store(CreateAllGamesRequest $request, Tournament $tournament): JsonResponse
    {
        DB::beginTransaction();
        $request->validated();
        try {
            $games = $this->gameService->createAllEliminationGames($tournament);
            DB::commit();
            return response()->json($games, 201);
         } catch (InvalidArgumentException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['message' => 'Not valid tournament type.'], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred while creating cup games.'], 500);
        }
    }

    public function index(Tournament $tournament): \Illuminate\Contracts\Foundation\Application|Factory|View|Application
    {
        try {
            $games = $this->gameService->getEliminationGames($tournament);

            if (request()->routeIs('admin.elimination.games')) {
                // Admin view
                return view('games.adminByElimination', compact('games'));
            }
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

    public function show(Tournament $tournament, EliminationGame $game): JsonResponse
    {
        $gameByTournamentAndId = $this->gameService->getGame($tournament, $game);

        return response()->json($gameByTournamentAndId);
    }

    /**
     * @throws Throwable
     */
    public function updateScore(UpdateEliminationGameScoreRequest $request, Tournament $tournament, EliminationGame $game): JsonResponse
    {
        DB::beginTransaction();
        $validatedData = $request->validated();
        try {
            $gameData = $this->gameService->updateGameScore($tournament, $game, $validatedData);
            DB::commit();
            return response()->json($gameData);
        } catch (NotFoundHttpException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['message' => 'Game not found for the given tournament and fixture.'], 404);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred while updating the game score.'], 500);
        }
    }
}
