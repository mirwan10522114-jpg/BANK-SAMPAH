<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'roles' => [UserRole::Nasabah->value],
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'roles' => [UserRole::Admin->value],
        ]);
    }

    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'roles' => [UserRole::Owner->value],
        ]);
    }

    public function nasabah(): static
    {
        return $this->state(fn (array $attributes) => [
            'roles' => [UserRole::Nasabah->value],
        ]);
    }

    public function koperasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'roles' => [UserRole::Koperasi->value],
        ]);
    }

    public function nasabahKoperasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'roles' => [UserRole::Nasabah->value, UserRole::Koperasi->value],
        ]);
    }
}
