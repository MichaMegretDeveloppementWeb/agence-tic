<?php

namespace Database\Seeders;

use App\Models\ActivityEntry;
use App\Models\Document;
use App\Models\Reminder;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $director = User::where('agent_code', 'DIR-GENESIS')->firstOrFail();
        $falcon = User::where('agent_code', 'AGT-FALCON')->firstOrFail();
        $shadow = User::where('agent_code', 'AGT-SHADOW')->firstOrFail();

        $reportRedEyes = Report::where('code', 'TIC-0001')->firstOrFail();
        $document = Document::where('title', 'Protocole de confinement standard')->firstOrFail();
        $globalReminder = Reminder::where('title', 'Réunion de coordination — 15 mars')->firstOrFail();

        $entries = [
            [
                'user_id' => $falcon->id,
                'subject_type' => Report::class,
                'subject_id' => $reportRedEyes->id,
                'event_type' => 'report_created',
                'message' => 'Agent Falcon a publié le rapport TIC-0001 Red Eyes dans la classe Agents pathogènes.',
            ],
            [
                'user_id' => $director->id,
                'subject_type' => User::class,
                'subject_id' => $shadow->id,
                'event_type' => 'user_created',
                'message' => 'Le Directeur G a créé le compte de l\'Agent Shadow (niveau 4).',
            ],
            [
                'user_id' => $director->id,
                'subject_type' => Report::class,
                'subject_id' => $reportRedEyes->id,
                'event_type' => 'special_permission_granted',
                'message' => 'Le Directeur G a accordé à l\'Agent Shadow un accès spécial au rapport TIC-0001 Red Eyes.',
            ],
            [
                'user_id' => $director->id,
                'subject_type' => Document::class,
                'subject_id' => $document->id,
                'event_type' => 'document_created',
                'message' => 'Le Directeur G a ajouté le document Protocole de confinement standard dans la bibliothèque.',
            ],
            [
                'user_id' => $director->id,
                'subject_type' => Reminder::class,
                'subject_id' => $globalReminder->id,
                'event_type' => 'global_reminder_created',
                'message' => 'Le Directeur G a publié un rappel global : Réunion de coordination — 15 mars.',
            ],
        ];

        foreach ($entries as $entry) {
            ActivityEntry::create(array_merge($entry, [
                'created_at' => now()->subDays(fake()->numberBetween(0, 14)),
            ]));
        }
    }
}
