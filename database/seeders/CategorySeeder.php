<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Agents pathogènes', 'slug' => 'agents-pathogenes', 'description' => 'Entités biologiques ou chimiques à risque de contamination.'],
            ['name' => 'Anomalies dimensionnelles', 'slug' => 'anomalies-dimensionnelles', 'description' => 'Phénomènes liés à des distorsions spatiales ou temporelles.'],
            ['name' => 'Entités cognitives', 'slug' => 'entites-cognitives', 'description' => 'Anomalies affectant la perception ou la conscience.'],
            ['name' => 'Objets autonomes', 'slug' => 'objets-autonomes', 'description' => 'Artefacts dotés de comportements indépendants.'],
            ['name' => 'Phénomènes électromagnétiques', 'slug' => 'phenomenes-electromagnetiques', 'description' => 'Perturbations du spectre électromagnétique d\'origine inconnue.'],
            ['name' => 'Zones de confinement', 'slug' => 'zones-de-confinement', 'description' => 'Lieux nécessitant un protocole de confinement permanent.'],
        ];

        foreach ($categories as $data) {
            Category::create($data);
        }
    }
}
