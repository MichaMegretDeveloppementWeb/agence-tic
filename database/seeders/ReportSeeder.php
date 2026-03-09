<?php

namespace Database\Seeders;

use App\Enums\ReportStatus;
use App\Enums\ThreatLevel;
use App\Models\Category;
use App\Models\Report;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::orderBy('id')->get();

        $reports = [
            [
                'code' => 'TIC-0001',
                'title' => 'Red Eyes',
                'category_id' => $categories[0]->id,
                'threat_level' => ThreatLevel::Critical,
                'accreditation_level' => 5,
                'description' => "Entité biologique découverte dans les sous-sols du site Bravo. Présente une luminescence oculaire rouge caractéristique et un comportement prédateur nocturne.\n\nPremière observation le 14 mars lors d'une inspection de routine. L'entité semble attirée par les sources de chaleur corporelle.",
                'procedures' => 'Maintenir une distance minimale de 10 mètres. Ne jamais établir de contact visuel prolongé. Confinement en cellule opaque de classe III avec température maintenue à 4°C.',
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
                'procedures' => 'Stockage en chambre anéchoïque de niveau 5. Aucun personnel non autorisé dans un rayon de 20 mètres. Inspection visuelle uniquement via caméra.',
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
                'procedures' => 'Périmètre de sécurité de 500 mètres. Protection auditive obligatoire de classe IV. Rotation du personnel toutes les 4 heures.',
                'notes' => 'Première manifestation observée à proximité du site Delta. Phénomène récurrent tous les 28 jours.',
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
                'notes' => 'Acquis via le réseau européen de récupération. Aucun incident majeur depuis le confinement.',
                'status' => ReportStatus::Contained,
            ],
            [
                'code' => 'TIC-0025',
                'title' => 'Signal Omega',
                'category_id' => $categories[4]->id,
                'threat_level' => ThreatLevel::Low,
                'accreditation_level' => 2,
                'description' => 'Émission radio intermittente captée sur une fréquence non répertoriée. Le signal semble contenir des séquences mathématiques structurées.',
                'procedures' => 'Surveillance passive continue. Aucune tentative de réponse autorisée sans validation du Directeur G.',
                'notes' => "Signal capté pour la première fois il y a 18 mois. Fréquence d'émission en augmentation.",
                'status' => ReportStatus::Active,
            ],
            [
                'code' => 'TIC-0031',
                'title' => 'Protocole Lazare',
                'category_id' => $categories[5]->id,
                'threat_level' => ThreatLevel::Extreme,
                'accreditation_level' => 8,
                'description' => 'Zone de confinement souterraine présentant des propriétés de régénération cellulaire accélérée. Tout organisme biologique introduit dans la zone subit des mutations imprévisibles.',
                'procedures' => "ACCÈS STRICTEMENT INTERDIT sans autorisation directe du Directeur G. Combinaison de protection intégrale obligatoire. Durée d'exposition maximale : 15 minutes.",
                'notes' => "Site découvert lors de travaux d'excavation. Classé immédiatement en confinement maximal.",
                'status' => ReportStatus::Active,
            ],
        ];

        foreach ($reports as $data) {
            Report::create($data);
        }
    }
}
