<?php

namespace App\Providers;

use App\Models\Game;
use App\Models\Tournament;
use Illuminate\Support\Facades;
use Illuminate\View\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Facades\View::composer('*', function (View $view) {
            $tournamentsWithFixtures = Game::join('tournaments', 'games.tournament_id', '=', 'tournaments.id')
                ->pluck('tournaments.name', 'games.tournament_id')
                ->unique()
                ->toArray();
            $tournaments = Tournament::all();

            $view->with([
                'tournamentsWithFixtures' => $tournamentsWithFixtures,
                'tournaments' => $tournaments
            ]);
        });
    }
}
