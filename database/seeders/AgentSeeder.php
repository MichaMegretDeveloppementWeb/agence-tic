<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
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
            User::factory()->create($profile);
        }

        User::factory()->inactive()->create([
            'agent_code' => 'AGT-GHOST',
            'name' => 'Agent Ghost',
            'accreditation_level' => 5,
        ]);
    }
}
