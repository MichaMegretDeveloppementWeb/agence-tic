<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Document;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $director = User::where('agent_code', 'DIR-GENESIS')->firstOrFail();
        $categories = Category::orderBy('id')->get();
        $reports = Report::orderBy('id')->get();

        $documentData = [
            ['title' => 'Protocole de confinement standard', 'accreditation_level' => 1, 'category_id' => $categories[5]->id, 'report_id' => $reports[0]->id],
            ['title' => 'Manuel de premiers secours — exposition anomalie', 'accreditation_level' => 2, 'category_id' => $categories[0]->id, 'report_id' => $reports[0]->id],
            ['title' => 'Cartographie des sites actifs', 'accreditation_level' => 6, 'category_id' => $categories[1]->id, 'report_id' => $reports[1]->id],
            ['title' => 'Rapport annuel — incidents 2025', 'accreditation_level' => 4, 'category_id' => $categories[4]->id],
            ['title' => 'Directives de communication externe', 'accreditation_level' => 3, 'category_id' => $categories[2]->id],
        ];

        foreach ($documentData as $data) {
            Document::factory()->create(array_merge($data, [
                'uploaded_by' => $director->id,
            ]));
        }
    }
}
