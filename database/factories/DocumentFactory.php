<?php

namespace Database\Factories;

use App\Enums\DocumentStatus;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->words(3, true)),
            'file_path' => 'documents/' . fake()->uuid() . '.pdf',
            'file_name' => fake()->word() . '.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(10_000, 5_000_000),
            'category_id' => Category::factory(),
            'accreditation_level' => fake()->numberBetween(1, 8),
            'uploaded_by' => User::factory(),
            'status' => DocumentStatus::Active,
            'notes' => fake()->optional(0.5)->paragraphs(2, true),
        ];
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DocumentStatus::Archived,
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DocumentStatus::Hidden,
        ]);
    }

    public function withLevel(int $level): static
    {
        return $this->state(fn (array $attributes) => [
            'accreditation_level' => $level,
        ]);
    }

    public function forReport(Report $report): static
    {
        return $this->state(fn (array $attributes) => [
            'report_id' => $report->id,
        ]);
    }
}
