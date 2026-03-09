<?php

namespace Database\Seeders;

use App\Enums\ReminderType;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReminderSeeder extends Seeder
{
    public function run(): void
    {
        $director = User::where('agent_code', 'DIR-GENESIS')->firstOrFail();
        $falcon = User::where('agent_code', 'AGT-FALCON')->firstOrFail();
        $cipher = User::where('agent_code', 'AGT-CIPHER')->firstOrFail();

        Reminder::factory()->create([
            'title' => 'Vérifier les relevés du site Bravo',
            'content' => 'Les capteurs thermiques doivent être recalibrés avant la fin du mois.',
            'type' => ReminderType::Personal,
            'created_by' => $falcon->id,
        ]);

        Reminder::factory()->create([
            'title' => 'Soumettre le rapport d\'observation',
            'content' => 'Le rapport sur le Signal Omega doit être finalisé et transmis.',
            'type' => ReminderType::Targeted,
            'created_by' => $director->id,
            'target_user_id' => $cipher->id,
        ]);

        Reminder::factory()->create([
            'title' => 'Réunion de coordination — 15 mars',
            'content' => 'Tous les agents de niveau 3 et supérieur sont convoqués en salle Delta à 14h00.',
            'type' => ReminderType::Global,
            'created_by' => $director->id,
        ]);

        Reminder::factory()->completed()->create([
            'title' => 'Mise à jour des accréditations trimestrielles',
            'created_by' => $director->id,
            'type' => ReminderType::Global,
        ]);
    }
}
