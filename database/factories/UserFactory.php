<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'agent_code' => 'AGT-'.strtoupper(Str::random(6)),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::Agent,
            'accreditation_level' => fake()->numberBetween(1, 8),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function directorG(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::DirectorG,
            'accreditation_level' => 8,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withLevel(int $level): static
    {
        return $this->state(fn (array $attributes) => [
            'accreditation_level' => $level,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
