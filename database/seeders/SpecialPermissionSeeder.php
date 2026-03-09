<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Report;
use App\Models\SpecialPermission;
use App\Models\User;
use Illuminate\Database\Seeder;

class SpecialPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $director = User::where('agent_code', 'DIR-GENESIS')->firstOrFail();
        $shadow = User::where('agent_code', 'AGT-SHADOW')->firstOrFail();
        $specter = User::where('agent_code', 'AGT-SPECTER')->firstOrFail();

        $reportRedEyes = Report::where('code', 'TIC-0001')->firstOrFail();
        $documentCarto = Document::where('title', 'Cartographie des sites actifs')->firstOrFail();

        // Agent Shadow (niveau 4) → accès au rapport TIC-0001 (niveau 5)
        SpecialPermission::create([
            'user_id' => $shadow->id,
            'permissionable_type' => Report::class,
            'permissionable_id' => $reportRedEyes->id,
            'granted_by' => $director->id,
        ]);

        // Agent Specter (niveau 2) → accès au document "Cartographie" (niveau 6)
        SpecialPermission::create([
            'user_id' => $specter->id,
            'permissionable_type' => Document::class,
            'permissionable_id' => $documentCarto->id,
            'granted_by' => $director->id,
        ]);
    }
}
