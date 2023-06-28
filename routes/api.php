<?php

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
Route::put('/tournaments/{tournament}', [TournamentController::class, 'updateAll'])->name('tournament.updateAll');
Route::delete('/tournaments/{tournament}', [TournamentController::class, 'destroy'])->name('tournament.destroy');

Route::get('/tournaments/{tournament}/teams', [TeamController::class, 'getAllTeams'])->name('team.getAll');
Route::post('/tournaments/{tournament}/teams', [TeamController::class, 'store'])->name('team.store');
Route::get('/tournaments/{tournament}/teams/{team}', [TeamController::class, 'show'])->name('team.show');
Route::delete('/tournaments/{tournament}/teams/{team}', [TeamController::class, 'destroy'])->name('team.destroy');
Route::put('/tournaments/{tournament}/teams/{team}', [TeamController::class, 'update'])->name('team.update');

