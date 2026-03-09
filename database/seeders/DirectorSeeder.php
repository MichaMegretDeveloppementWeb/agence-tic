<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DirectorSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->directorG()->create([
            'agent_code' => 'DIR-GENESIS',
            'name' => 'Directeur Général',
            'email' => 'directeur@agence-tic.fr',
        ]);
    }
}
