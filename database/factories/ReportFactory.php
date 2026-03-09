<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Enums\ThreatLevel;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    private static int $codeIncrement = 1;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'code' => 'TIC-'.str_pad((string) self::$codeIncrement++, 4, '0', STR_PAD_LEFT),
            'title' => ucfirst(fake()->words(3, true)),
            'category_id' => Category::factory(),
            'threat_level' => fake()->randomElement(ThreatLevel::cases()),
            'accreditation_level' => fake()->numberBetween(1, 8),
            'description' => fake()->paragraphs(3, true),
            'procedures' => fake()->paragraphs(2, true),
            'notes' => fake()->paragraphs(2, true),
            'status' => ReportStatus::Active,
        ];
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::Archived,
        ]);
    }

    public function contained(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::Contained,
        ]);
    }

    public function withLevel(int $level): static
    {
        return $this->state(fn (array $attributes) => [
            'accreditation_level' => $level,
        ]);
    }
}
