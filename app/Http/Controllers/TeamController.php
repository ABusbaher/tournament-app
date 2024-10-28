<?php

namespace App\Http\Controllers;

use App\Exceptions\TournamentExistsException;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\TeamService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class TeamController extends Controller
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function getAllTeams(Tournament $tournament): JsonResponse
    {
        $teams = $this->teamService->getAllTeamsByTournament($tournament);

        return response()->json($teams);
    }

    public function index(): View
    {
        return view('team.all');
    }

    public function show(Tournament $tournament, Team $team): JsonResponse
    {
        $team = $this->teamService->getTeam($tournament, $team);

        return response()->json($team);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(CreateTeamRequest $request, Tournament $tournament): JsonResponse
    {
        $this->authorize('adminAccess', $request->user());
        try {
            $team = $this->teamService->createTeam($request->validated(), $tournament);
            return response()->json($team, 201);
        } catch (TournamentExistsException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred while creating a team.'], 500);
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateTeamRequest $teamRequest, Tournament $tournament, Team $team): JsonResponse
    {
        $this->authorize('adminAccess', $teamRequest->user());
        $validatedData = $teamRequest->validated();
        $teamData = $this->teamService->updateTeam($team, $validatedData);

        return response()->json($teamData);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Request $request, Tournament $tournament, Team $team): JsonResponse
    {
        $this->authorize('adminAccess', $request->user());
        try {
            $this->teamService->deleteTeam($team, $tournament);
            return response()->json(['message' => 'Team deleted successfully'], 204);
        } catch (TournamentExistsException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting a team.'], 500);
        }

    }
}
