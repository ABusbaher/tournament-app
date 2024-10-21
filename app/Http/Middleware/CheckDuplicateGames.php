<?php

namespace App\Http\Middleware;

use App\Models\EliminationGame;
use App\Models\Game;
use App\Models\Tournament;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDuplicateGames
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tournamentId = $request->input('tournament_id');
        $tournament = Tournament::find($tournamentId);

        if ($tournament?->type === 'league') {
            if (Game::where('tournament_id', $tournamentId)->exists()) {
                return response()->json(['message' => 'Fixtures for this tournament already exist!'], 403);
            }
        } else if ($tournament?->type === 'elimination') {
            if (EliminationGame::where('tournament_id', $tournamentId)->exists()) {
                return response()->json(['message' => 'Elimination cup for this tournament already exist!'], 403);
            }
        }
        return $next($request);
    }
}
