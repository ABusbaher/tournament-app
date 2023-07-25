<?php

use App\Http\Controllers\EliminationGameController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournament.store');
Route::get('/tournaments', [TournamentController::class, 'getAll'])->name('tournament.getAll');
Route::get('/tournaments/{tournament}', [TournamentController::class, 'show'])->name('tournament.show');
Route::patch('/tournaments/{tournament}', [TournamentController::class, 'updateName'])->name('tournament.updateName');
Route::put('/tournaments/{tournament}', [TournamentController::class, 'updateAll'])->name('tournament.updateAll')->middleware('check.duplicate.games');
Route::delete('/tournaments/{tournament}', [TournamentController::class, 'destroy'])->name('tournament.destroy');

Route::get('/tournaments/{tournament}/teams', [TeamController::class, 'getAllTeams'])->name('team.getAll');
Route::post('/tournaments/{tournament}/teams', [TeamController::class, 'store'])->name('team.store');
Route::get('/tournaments/{tournament}/teams/{team}', [TeamController::class, 'show'])->name('team.show');
Route::delete('/tournaments/{tournament}/teams/{team}', [TeamController::class, 'destroy'])->name('team.destroy');
Route::put('/tournaments/{tournament}/teams/{team}', [TeamController::class, 'update'])->name('team.update');

Route::get('/tournaments/{tournament}/fixtures/{fixture}', [GameController::class, 'getByFixtures'])->name('games.by_fixture');
Route::get('/tournaments/{tournament}/games/{game}', [GameController::class, 'show'])->name('game.show');
Route::patch('/tournaments/{tournament}/games/{game}', [GameController::class, 'updateScore'])->name('game.updateScore');
Route::post('/tournaments/{tournament}/games', [GameController::class, 'store'])->name('games.create.all')->middleware('check.duplicate.games');
Route::get('/tournaments/{tournament}/table', [GameController::class, 'showTable'])->name('games.table');

Route::post('/tournaments/{tournament}/elimination-games', [EliminationGameController::class, 'store'])->name('elimination-games.create.all')->middleware('check.duplicate.games');;
