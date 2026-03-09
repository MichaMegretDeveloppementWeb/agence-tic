<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityEntry>
 */
class ActivityEntryFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subject_type' => Report::class,
            'subject_id' => Report::factory(),
            'event_type' => 'report_created',
            'message' => fake()->sentence(),
        ];
    }
}
