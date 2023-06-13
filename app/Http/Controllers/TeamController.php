<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTeamRequest;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
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
        $validatedData = $request->validated();
        $team = $this->teamService->createTeam($validatedData);

        return response()->json($team, 201);
    }
}
