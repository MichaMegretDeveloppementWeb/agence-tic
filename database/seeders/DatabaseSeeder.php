<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\ReminderType;
use App\Enums\ReportStatus;
use App\Enums\ThreatLevel;
use App\Models\ActivityEntry;
use App\Models\Application;
use App\Models\Category;
use App\Models\Document;
use App\Models\Reminder;
use App\Models\Report;
use App\Models\SpecialPermission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        /* ----------------------------------------------------------------
         |  Directeur G
         | ---------------------------------------------------------------- */

        $director = User::factory()->directorG()->create([
            'agent_code' => 'DIR-GENESIS',
            'name' => 'Directeur Général',
            'email' => 'directeur@agence-tic.fr',
        ]);

        /* ----------------------------------------------------------------
         |  Agents (niveaux variés)
         | ---------------------------------------------------------------- */

        $agents = collect();

        $agentProfiles = [
            ['agent_code' => 'AGT-FALCON', 'name' => 'Agent Falcon', 'accreditation_level' => 6],
            ['agent_code' => 'AGT-SHADOW', 'name' => 'Agent Shadow', 'accreditation_level' => 4],
            ['agent_code' => 'AGT-NOVA', 'name' => 'Agent Nova', 'accreditation_level' => 7],
            ['agent_code' => 'AGT-SPECTER', 'name' => 'Agent Specter', 'accreditation_level' => 2],
            ['agent_code' => 'AGT-CIPHER', 'name' => 'Agent Cipher', 'accreditation_level' => 5],
            ['agent_code' => 'AGT-VORTEX', 'name' => 'Agent Vortex', 'accreditation_level' => 3],
            ['agent_code' => 'AGT-ZERO', 'name' => 'Agent Zero', 'accreditation_level' => 8],
            ['agent_code' => 'AGT-DRIFT', 'name' => 'Agent Drift', 'accreditation_level' => 1],
        ];

        foreach ($agentProfiles as $profile) {
            $agents->push(User::factory()->create($profile));
        }

        // Un agent inactif
        User::factory()->inactive()->create([
            'agent_code' => 'AGT-GHOST',
            'name' => 'Agent Ghost',
            'accreditation_level' => 5,
        ]);

        /* ----------------------------------------------------------------
         |  Catégories
         | ---------------------------------------------------------------- */

        $categories = collect([
            ['name' => 'Agents pathogènes', 'slug' => 'agents-pathogenes', 'description' => 'Entités biologiques ou chimiques à risque de contamination.'],
            ['name' => 'Anomalies dimensionnelles', 'slug' => 'anomalies-dimensionnelles', 'description' => 'Phénomènes liés à des distorsions spatiales ou temporelles.'],
            ['name' => 'Entités cognitives', 'slug' => 'entites-cognitives', 'description' => 'Anomalies affectant la perception ou la conscience.'],
            ['name' => 'Objets autonomes', 'slug' => 'objets-autonomes', 'description' => 'Artefacts dotés de comportements indépendants.'],
            ['name' => 'Phénomènes électromagnétiques', 'slug' => 'phenomenes-electromagnetiques', 'description' => 'Perturbations du spectre électromagnétique d\'origine inconnue.'],
            ['name' => 'Zones de confinement', 'slug' => 'zones-de-confinement', 'description' => 'Lieux nécessitant un protocole de confinement permanent.'],
        ])->map(fn (array $data) => Category::create($data));

        /* ----------------------------------------------------------------
         |  Rapports
         | ---------------------------------------------------------------- */

        $reports = collect([
            [
                'code' => 'TIC-0001',
                'title' => 'Red Eyes',
                'category_id' => $categories[0]->id,
                'threat_level' => ThreatLevel::Critical,
                'accreditation_level' => 5,
                'description' => "Entité biologique découverte dans les sous-sols du site Bravo. Présente une luminescence oculaire rouge caractéristique et un comportement prédateur nocturne.\n\nPremière observation le 14 mars lors d'une inspection de routine. L'entité semble attirée par les sources de chaleur corporelle.",
                'procedures' => "Maintenir une distance minimale de 10 mètres. Ne jamais établir de contact visuel prolongé. Confinement en cellule opaque de classe III avec température maintenue à 4°C.",
                'notes' => "14/03 — Première observation par l'Agent Falcon.\n22/03 — Tentative de confinement, un agent blessé.\n01/04 — Confinement réussi, protocole Sigma activé.",
                'status' => ReportStatus::Contained,
            ],
            [
                'code' => 'TIC-0007',
                'title' => 'Miroir d\'Erebus',
                'category_id' => $categories[1]->id,
                'threat_level' => ThreatLevel::Extreme,
                'accreditation_level' => 7,
                'description' => "Miroir ancien d'origine inconnue présentant des propriétés de réflexion anormales. Les reflets ne correspondent pas toujours à la réalité observée.\n\nL'objet semble créer des micro-fissures dimensionnelles lorsqu'il est exposé à certaines fréquences sonores.",
                'procedures' => "Stockage en chambre anéchoïque de niveau 5. Aucun personnel non autorisé dans un rayon de 20 mètres. Inspection visuelle uniquement via caméra.",
                'notes' => "Récupéré lors de l'opération Nyx. Trois incidents de distorsion enregistrés depuis l'acquisition.",
                'status' => ReportStatus::Active,
            ],
            [
                'code' => 'TIC-0012',
                'title' => 'Murmures de Babel',
                'category_id' => $categories[2]->id,
                'threat_level' => ThreatLevel::High,
                'accreditation_level' => 4,
                'description' => "Phénomène auditif collectif affectant les personnes dans un rayon de 500 mètres autour du point d'origine. Les sujets rapportent entendre des voix dans des langues inconnues.",
                'procedures' => "Périmètre de sécurité de 500 mètres. Protection auditive obligatoire de classe IV. Rotation du personnel toutes les 4 heures.",
                'notes' => "Première manifestation observée à proximité du site Delta. Phénomène récurrent tous les 28 jours.",
                'status' => ReportStatus::Active,
            ],
            [
                'code' => 'TIC-0019',
                'title' => 'Automate de Prague',
                'category_id' => $categories[3]->id,
                'threat_level' => ThreatLevel::Moderate,
                'accreditation_level' => 3,
                'description' => "Automate mécanique du XVIIe siècle capable de mouvement autonome sans source d'énergie identifiable. Comportement généralement docile mais imprévisible.",
                'procedures' => "Confinement standard. L'objet doit être remonté manuellement toutes les 72 heures pour éviter un comportement erratique.",
                'notes' => "Acquis via le réseau européen de récupération. Aucun incident majeur depuis le confinement.",
                'status' => ReportStatus::Contained,
            ],
            [
                'code' => 'TIC-0025',
                'title' => 'Signal Omega',
                'category_id' => $categories[4]->id,
                'threat_level' => ThreatLevel::Low,
                'accreditation_level' => 2,
                'description' => "Émission radio intermittente captée sur une fréquence non répertoriée. Le signal semble contenir des séquences mathématiques structurées.",
                'procedures' => "Surveillance passive continue. Aucune tentative de réponse autorisée sans validation du Directeur G.",
                'notes' => "Signal capté pour la première fois il y a 18 mois. Fréquence d'émission en augmentation.",
                'status' => ReportStatus::Active,
            ],
            [
                'code' => 'TIC-0031',
                'title' => 'Protocole Lazare',
                'category_id' => $categories[5]->id,
                'threat_level' => ThreatLevel::Extreme,
                'accreditation_level' => 8,
                'description' => "Zone de confinement souterraine présentant des propriétés de régénération cellulaire accélérée. Tout organisme biologique introduit dans la zone subit des mutations imprévisibles.",
                'procedures' => "ACCÈS STRICTEMENT INTERDIT sans autorisation directe du Directeur G. Combinaison de protection intégrale obligatoire. Durée d'exposition maximale : 15 minutes.",
                'notes' => "Site découvert lors de travaux d'excavation. Classé immédiatement en confinement maximal.",
                'status' => ReportStatus::Active,
            ],
        ]);

        $createdReports = $reports->map(fn (array $data) => Report::create($data));

        /* ----------------------------------------------------------------
         |  Documents
         | ---------------------------------------------------------------- */

        $documentData = [
            ['title' => 'Protocole de confinement standard', 'accreditation_level' => 1, 'category_id' => $categories[5]->id, 'report_id' => $createdReports[0]->id],
            ['title' => 'Manuel de premiers secours — exposition anomalie', 'accreditation_level' => 2, 'category_id' => $categories[0]->id, 'report_id' => $createdReports[0]->id],
            ['title' => 'Cartographie des sites actifs', 'accreditation_level' => 6, 'category_id' => $categories[1]->id, 'report_id' => $createdReports[1]->id],
            ['title' => 'Rapport annuel — incidents 2025', 'accreditation_level' => 4, 'category_id' => $categories[4]->id],
            ['title' => 'Directives de communication externe', 'accreditation_level' => 3, 'category_id' => $categories[2]->id],
        ];

        $createdDocuments = collect($documentData)->map(fn (array $data) => Document::factory()->create(array_merge($data, [
            'uploaded_by' => $director->id,
        ])));

        /* ----------------------------------------------------------------
         |  Permissions spéciales
         | ---------------------------------------------------------------- */

        // Agent Shadow (niveau 4) → accès au rapport TIC-0001 (niveau 5)
        SpecialPermission::create([
            'user_id' => $agents[1]->id,
            'permissionable_type' => Report::class,
            'permissionable_id' => $createdReports[0]->id,
            'granted_by' => $director->id,
        ]);

        // Agent Specter (niveau 2) → accès au document "Cartographie" (niveau 6)
        SpecialPermission::create([
            'user_id' => $agents[3]->id,
            'permissionable_type' => Document::class,
            'permissionable_id' => $createdDocuments[2]->id,
            'granted_by' => $director->id,
        ]);

        /* ----------------------------------------------------------------
         |  Rappels
         | ---------------------------------------------------------------- */

        // Rappel personnel
        Reminder::factory()->create([
            'title' => 'Vérifier les relevés du site Bravo',
            'content' => 'Les capteurs thermiques doivent être recalibrés avant la fin du mois.',
            'type' => ReminderType::Personal,
            'created_by' => $agents[0]->id,
        ]);

        // Rappel ciblé
        Reminder::factory()->create([
            'title' => 'Soumettre le rapport d\'observation',
            'content' => 'Le rapport sur le Signal Omega doit être finalisé et transmis.',
            'type' => ReminderType::Targeted,
            'created_by' => $director->id,
            'target_user_id' => $agents[4]->id,
        ]);

        // Rappel global
        Reminder::factory()->create([
            'title' => 'Réunion de coordination — 15 mars',
            'content' => 'Tous les agents de niveau 3 et supérieur sont convoqués en salle Delta à 14h00.',
            'type' => ReminderType::Global,
            'created_by' => $director->id,
        ]);

        // Rappel terminé
        Reminder::factory()->completed()->create([
            'title' => 'Mise à jour des accréditations trimestrielles',
            'created_by' => $director->id,
            'type' => ReminderType::Global,
        ]);

        /* ----------------------------------------------------------------
         |  Fil d'activité
         | ---------------------------------------------------------------- */

        $activityData = [
            [
                'user_id' => $agents[0]->id,
                'subject_type' => Report::class,
                'subject_id' => $createdReports[0]->id,
                'event_type' => 'report_created',
                'message' => 'Agent Falcon a publié le rapport TIC-0001 Red Eyes dans la classe Agents pathogènes.',
            ],
            [
                'user_id' => $director->id,
                'subject_type' => User::class,
                'subject_id' => $agents[1]->id,
                'event_type' => 'user_created',
                'message' => 'Le Directeur G a créé le compte de l\'Agent Shadow (niveau 4).',
            ],
            [
                'user_id' => $director->id,
                'subject_type' => Report::class,
                'subject_id' => $createdReports[0]->id,
                'event_type' => 'special_permission_granted',
                'message' => 'Le Directeur G a accordé à l\'Agent Shadow un accès spécial au rapport TIC-0001 Red Eyes.',
            ],
            [
                'user_id' => $director->id,
                'subject_type' => Document::class,
                'subject_id' => $createdDocuments[0]->id,
                'event_type' => 'document_created',
                'message' => 'Le Directeur G a ajouté le document Protocole de confinement standard dans la bibliothèque.',
            ],
            [
                'user_id' => $director->id,
                'subject_type' => Reminder::class,
                'subject_id' => 3,
                'event_type' => 'global_reminder_created',
                'message' => 'Le Directeur G a publié un rappel global : Réunion de coordination — 15 mars.',
            ],
        ];

        foreach ($activityData as $entry) {
            ActivityEntry::create(array_merge($entry, [
                'created_at' => now()->subDays(fake()->numberBetween(0, 14)),
            ]));
        }

        /* ----------------------------------------------------------------
         |  Candidatures
         | ---------------------------------------------------------------- */

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
