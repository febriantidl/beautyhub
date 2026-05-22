<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * PERHATIAN: Seeder ini hanya untuk setup database BARU.
     * Jika database sudah berisi data dari mobile (Flutter),
     * JANGAN jalankan migrate:fresh — cukup jalankan:
     *   php artisan migrate  (tanpa --fresh)
     *
     * Untuk reset total (dev only):
     *   php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('chatbot_logs')->truncate();
        DB::table('reviews')->truncate();
        DB::table('bookings')->truncate();
        DB::table('portfolios')->truncate();
        DB::table('services')->truncate();
        DB::table('muas')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ── Admin ─────────────────────────────────────────────────
        DB::table('users')->insert([
            'name'       => 'Admin BeautyHub',
            'email'      => 'admin@beautyhub.com',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'phone'      => '081234567890',
            'is_active'  => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ── MUA ───────────────────────────────────────────────────
        $muaList = [
            ['name' => 'Iah Sopiyah',     'email' => 'iah@beautyhub.com',   'phone' => '081234567801', 'location' => 'Kota Indramayu', 'exp' => 5, 'bio' => 'MUA profesional spesialis makeup wedding adat Jawa dan Sunda.', 'rating' => 4.9, 'reviews' => 48],
            ['name' => 'Siti Aminah',     'email' => 'siti@beautyhub.com',  'phone' => '081234567802', 'location' => 'Kota Indramayu', 'exp' => 3, 'bio' => 'Spesialis makeup wisuda dan pesta modern.',                   'rating' => 4.7, 'reviews' => 32],
            ['name' => 'Mifta Hani',      'email' => 'mifta@beautyhub.com', 'phone' => '081234567803', 'location' => 'Kota Indramayu', 'exp' => 7, 'bio' => 'MUA berpengalaman 7 tahun, wedding hingga photoshoot.',       'rating' => 4.8, 'reviews' => 65],
            ['name' => 'Indra Aliza',     'email' => 'indra@beautyhub.com', 'phone' => '081234567804', 'location' => 'Kota Cirebon',   'exp' => 4, 'bio' => 'Spesialis bridal makeup airbrush, bersertifikat BNSP.',       'rating' => 4.6, 'reviews' => 27],
            ['name' => 'Febriyanti Dewi', 'email' => 'febri@beautyhub.com', 'phone' => '081234567805', 'location' => 'Kota Cirebon',   'exp' => 2, 'bio' => 'MUA muda dengan style modern dan kekinian.',                  'rating' => 4.5, 'reviews' => 18],
        ];

        foreach ($muaList as $m) {
            $userId = DB::table('users')->insertGetId([
                'name' => $m['name'], 'email' => $m['email'],
                'password' => Hash::make('password'), 'role' => 'mua',
                'phone' => $m['phone'], 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $muaId = DB::table('muas')->insertGetId([
                'user_id' => $userId, 'location' => $m['location'],
                'bio' => $m['bio'], 'experience_years' => $m['exp'],
                'rating' => $m['rating'], 'total_reviews' => $m['reviews'],
                'is_verified' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->insertServices($muaId);
        }

        // ── Customer ──────────────────────────────────────────────
        foreach ([
            ['Rina Marlina', 'rina@gmail.com', '085678901201'],
            ['Dewi Sartika', 'dewi@gmail.com', '085678901202'],
            ['Ayu Lestari',  'ayu@gmail.com',  '085678901203'],
        ] as [$name, $email, $phone]) {
            DB::table('users')->insertGetId([
                'name' => $name, 'email' => $email,
                'password' => Hash::make('password'), 'role' => 'customer',
                'phone' => $phone, 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $this->command->info('');
        $this->command->info('✅ Seed selesai!');
        $this->command->info('   Admin   → admin@beautyhub.com / password');
        $this->command->info('   MUA     → iah@beautyhub.com   / password');
        $this->command->info('   Customer→ rina@gmail.com      / password');
        $this->command->info('');
    }

    private function insertServices(int $muaId): void
    {
        foreach ([
            ['Makeup Wedding',    'wedding',    1500000, 'Rias pengantin adat dan modern, tahan lama.'],
            ['Makeup Wisuda',     'graduation',  350000, 'Makeup fresh natural untuk momen wisuda.'],
            ['Makeup Party',      'party',       400000, 'Makeup glam elegan untuk pesta malam.'],
            ['Makeup Photoshoot', 'photoshoot',  500000, 'Makeup profesional untuk sesi foto.'],
            ['Makeup Formal',     'formal',      300000, 'Makeup rapi profesional untuk acara resmi.'],
        ] as [$name, $cat, $price, $desc]) {
            DB::table('services')->insert([
                'mua_id' => $muaId, 'name' => $name, 'category' => $cat,
                'price' => $price, 'description' => $desc, 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}
