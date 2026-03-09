<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            DirectorSeeder::class,
            AgentSeeder::class,
            CategorySeeder::class,
            ReportSeeder::class,
            DocumentSeeder::class,
            SpecialPermissionSeeder::class,
            ReminderSeeder::class,
            ActivitySeeder::class,
            ApplicationSeeder::class,
        ]);
    }
}
