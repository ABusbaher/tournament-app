<?php

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
Route::patch('/tournaments/{tournament}', [TournamentController::class, 'update'])->name('tournament.update');
