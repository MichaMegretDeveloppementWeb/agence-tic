<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpecialPermission>
 */
class SpecialPermissionFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'permissionable_type' => Report::class,
            'permissionable_id' => Report::factory(),
            'granted_by' => User::factory()->directorG(),
        ];
    }

    public function forReport(Report $report): static
    {
        return $this->state(fn (array $attributes) => [
            'permissionable_type' => Report::class,
            'permissionable_id' => $report->id,
        ]);
    }

    public function forDocument(\App\Models\Document $document): static
    {
        return $this->state(fn (array $attributes) => [
            'permissionable_type' => \App\Models\Document::class,
            'permissionable_id' => $document->id,
        ]);
    }
}
