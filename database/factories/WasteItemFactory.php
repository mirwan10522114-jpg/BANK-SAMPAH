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