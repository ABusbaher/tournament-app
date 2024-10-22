<?php

namespace Database\Seeders;

use App\Enums\TournamentTypeEnum;
use App\Models\Tournament;
use Illuminate\Database\Seeder;

class TournamentSeeder extends Seeder
{
    public function run()
    {
        Tournament::create([
           'name' => 'Pesoholičari',
           'type' => TournamentTypeEnum::LEAGUE->value,
           'rounds' => 2
        ]);

        Tournament::create([
            'name' => 'Tournament 2',
            'type' => TournamentTypeEnum::ELIMINATION->value,
            'rounds' => 1
        ]);
    }

}
