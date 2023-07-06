<?php

namespace App\Http\Middleware;

use App\Models\Game;
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
        $existingMatches = Game::where('tournament_id', $tournamentId)->exists();
        if ($existingMatches) {
            return response()->json(['message' => 'Fixtures for this tournament already exist!'], 403);
        }
        return $next($request);
    }
}
