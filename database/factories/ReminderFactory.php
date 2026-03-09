<?php

namespace Database\Factories;

use App\Enums\ReminderType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reminder>
 */
class ReminderFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->words(4, true)),
            'content' => fake()->sentence(),
            'type' => ReminderType::Personal,
            'created_by' => User::factory(),
            'target_user_id' => null,
            'is_completed' => false,
            'due_date' => fake()->optional(0.7)->dateTimeBetween('now', '+30 days'),
            'priority' => null,
        ];
    }

    public function targeted(User $target): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ReminderType::Targeted,
            'target_user_id' => $target->id,
        ]);
    }

    public function global(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ReminderType::Global,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
        ]);
    }
}
