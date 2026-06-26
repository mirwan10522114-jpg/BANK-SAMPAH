<<<<<<< HEAD
public function run(): void
    {
        $this->command->info('Memulai proses seeder 1000 data masif...');

        // 0. Buat Akun Admin Utama
        $this->command->info('Membangun akun Admin...');
        User::factory()->create([
            'name' => 'Admin Naufal',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password123'), // Ini password untuk login
            'role' => UserRole::ADMIN ?? 'admin', // Menyesuaikan dengan Enum role Admin
        ]);

        // 1. Buat 1000 Nasabah (User)
        $this->command->info('Membangun 1000 data Nasabah...');
        $nasabahs = User::factory()->nasabah()->count(1000)->create();
        
        // ... (kode sisanya biarkan sama persis seperti sebelumnya)
=======
<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\WasteCategory;
use App\Models\WasteItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<WasteItem>
 */
class WasteItemFactory extends Factory
{
    public function definition(): array
    {
        $name = ucfirst(fake()->unique()->words(2, true));
        // Ubah format kombinasi menjadi 3 huruf dan 4 angka agar kapasitas uniknya besar
        $code = strtoupper(fake()->unique()->bothify('???-####'));

        return [
            'waste_category_id' => WasteCategory::factory(),
            'code' => $code,
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(4),
            'unit' => 'kg',
            'price_per_unit' => fake()->randomFloat(2, 500, 20000),
            'description' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea
