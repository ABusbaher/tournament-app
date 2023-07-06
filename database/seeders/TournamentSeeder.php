<?php

namespace Database\Seeders;

use App\Models\Tournament;
use Illuminate\Database\Seeder;

class TournamentSeeder extends Seeder
{
    public function run()
    {
        Tournament::create([
           'name' => 'Pesoholičari',
           'type' => 'league',
           'rounds' => 2
        ]);

        Tournament::create([
            'name' => 'Tournament 2',
            'type' => 'league',
            'rounds' => 1
        ]);
    }

}
