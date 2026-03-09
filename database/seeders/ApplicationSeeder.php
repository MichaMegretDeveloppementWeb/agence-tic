<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        Application::factory()->count(3)->create();

        Application::factory()->accepted()->create([
            'name' => 'Marie Lefort',
            'email' => 'marie.lefort@example.com',
            'motivation' => 'Passionnée par l\'investigation et la gestion de situations complexes, je souhaite mettre mes compétences au service de l\'Agence TIC.',
        ]);

        Application::factory()->rejected()->create([
            'name' => 'Paul Renard',
            'email' => 'paul.renard@example.com',
        ]);
    }
}
