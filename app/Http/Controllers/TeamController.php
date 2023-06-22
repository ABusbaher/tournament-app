<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function getAllTeams(): JsonResponse
    {
        $teams = Team::with('tournament')->get();

        return response()->json($teams);
    }

    public function index(): View
    {
        return view('team.all');
    }

    public function store(CreateTeamRequest $request): JsonResponse
    {
        $team = $this->teamService->createTeam($request->validated());

        return response()->json($team, 201);
    }

    public function show(Tournament $tournament, Team $team): JsonResponse
    {
        return response()->json($team);
    }

    public function update(UpdateTeamRequest $teamRequest, Tournament $tournament, Team $team): JsonResponse
    {

//        $validatedData = $request->validate([
//            'name' => 'required|string|min:3|max:255',
//            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
//        ]);
//        dd($request->input());
//        dd($teamRequest->validated());

        $validatedData = $teamRequest->validated();
        $teamData = $this->teamService->updateTeam($team, $validatedData);

        return response()->json($teamData);
    }

    public function destroy(Tournament $tournament, Team $team): JsonResponse
    {
        $this->teamService->deleteTeam($team);

        return response()->json(['message' => 'Team deleted successfully'], 204);
    }
}
