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
<<<<<<< HEAD
    protected static ?string $password;

=======
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
<<<<<<< HEAD
            'roles' => [UserRole::Nasabah->value],
=======
            'role' => UserRole::Nasabah,
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
            'remember_token' => Str::random(10),
        ];
    }

<<<<<<< HEAD
=======
    /**
     * Indicate that the model's email address should be unverified.
     */
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
<<<<<<< HEAD
            'roles' => [UserRole::Admin->value],
=======
            'role' => UserRole::Admin,
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
        ]);
    }

    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
<<<<<<< HEAD
            'roles' => [UserRole::Owner->value],
=======
            'role' => UserRole::Owner,
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
        ]);
    }

    public function nasabah(): static
    {
        return $this->state(fn (array $attributes) => [
<<<<<<< HEAD
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
=======
            'role' => UserRole::Nasabah,
        ]);
    }

>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
}
