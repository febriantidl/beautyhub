<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Matikan foreign key check sementara agar bisa truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('chatbot_logs')->truncate();
        DB::table('reviews')->truncate();
        DB::table('bookings')->truncate();
        DB::table('portfolios')->truncate();
        DB::table('services')->truncate();
        DB::table('muas')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ── ADMIN ──────────────────────────────────────────────────
        DB::table('users')->insert([
            'name'       => 'Admin BeautyHub',
            'email'      => 'admin@beautyhub.com',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'phone'      => '081234567890',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── MUA ACCOUNTS ───────────────────────────────────────────
        $muaList = [
            [
                'name'     => 'Iah Sopiyah',
                'email'    => 'iah@beautyhub.com',
                'phone'    => '081234567801',
                'location' => 'Kota Indramayu',
                'exp'      => 5,
                'bio'      => 'MUA profesional spesialis makeup wedding adat Jawa dan Sunda. Melayani dengan sepenuh hati.',
                'rating'   => 4.9,
                'reviews'  => 48,
            ],
            [
                'name'     => 'Siti Aminah',
                'email'    => 'siti@beautyhub.com',
                'phone'    => '081234567802',
                'location' => 'Kota Indramayu',
                'exp'      => 3,
                'bio'      => 'Spesialis makeup wisuda dan pesta dengan sentuhan modern dan elegan.',
                'rating'   => 4.7,
                'reviews'  => 32,
            ],
            [
                'name'     => 'Mifta Hani',
                'email'    => 'mifta@beautyhub.com',
                'phone'    => '081234567803',
                'location' => 'Kota Indramayu',
                'exp'      => 7,
                'bio'      => 'MUA berpengalaman 7 tahun, melayani berbagai acara dari wedding hingga photoshoot profesional.',
                'rating'   => 4.8,
                'reviews'  => 65,
            ],
            [
                'name'     => 'Indra Aliza',
                'email'    => 'indra@beautyhub.com',
                'phone'    => '081234567804',
                'location' => 'Kota Cirebon',
                'exp'      => 4,
                'bio'      => 'Spesialis bridal makeup dengan teknik airbrush profesional. Bersertifikat BNSP.',
                'rating'   => 4.6,
                'reviews'  => 27,
            ],
            [
                'name'     => 'Febriyanti Dewi',
                'email'    => 'febri@beautyhub.com',
                'phone'    => '081234567805',
                'location' => 'Kota Cirebon',
                'exp'      => 2,
                'bio'      => 'MUA muda berbakat dengan style modern dan kekinian. Harga terjangkau, hasil memuaskan.',
                'rating'   => 4.5,
                'reviews'  => 18,
            ],
        ];

        foreach ($muaList as $mData) {
            // Insert user
            $userId = DB::table('users')->insertGetId([
                'name'       => $mData['name'],
                'email'      => $mData['email'],
                'password'   => Hash::make('password'),
                'role'       => 'mua',
                'phone'      => $mData['phone'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert mua profile
            $muaId = DB::table('muas')->insertGetId([
                'user_id'          => $userId,
                'location'         => $mData['location'],
                'bio'              => $mData['bio'],
                'experience_years' => $mData['exp'],
                'rating'           => $mData['rating'],
                'total_reviews'    => $mData['reviews'],
                'is_verified'      => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // Insert services untuk MUA ini
            $this->insertServices($muaId);
        }

        // ── CUSTOMER ACCOUNTS ──────────────────────────────────────
        $customers = [
            ['name' => 'Rina Marlina',    'email' => 'rina@gmail.com',  'phone' => '085678901201'],
            ['name' => 'Dewi Sartika',    'email' => 'dewi@gmail.com',  'phone' => '085678901202'],
            ['name' => 'Ayu Lestari',     'email' => 'ayu@gmail.com',   'phone' => '085678901203'],
            ['name' => 'Fitri Handayani', 'email' => 'fitri@gmail.com', 'phone' => '085678901204'],
            ['name' => 'Nisa Rahayu',     'email' => 'nisa@gmail.com',  'phone' => '085678901205'],
        ];

        $customerIds = [];
        foreach ($customers as $cData) {
            $customerIds[] = DB::table('users')->insertGetId([
                'name'       => $cData['name'],
                'email'      => $cData['email'],
                'password'   => Hash::make('password'),
                'role'       => 'customer',
                'phone'      => $cData['phone'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ── SAMPLE BOOKINGS ────────────────────────────────────────
        $muas    = DB::table('muas')->get();
        $statuses = ['pending', 'approved', 'completed'];

        foreach ($customerIds as $i => $custId) {
            $mua     = $muas[$i % $muas->count()];
            $service = DB::table('services')->where('mua_id', $mua->id)->first();
            if (!$service) continue;

            $status = $statuses[$i % 3];
            $code   = strtoupper(substr(md5($i . 'beautyhub' . time()), 0, 6));

            $bookingId = DB::table('bookings')->insertGetId([
                'user_id'          => $custId,
                'mua_id'           => $mua->id,
                'service_id'       => $service->id,
                'booking_date'     => now()->subDays(rand(5, 30))->toDateString(),
                'event_date'       => now()->addDays(rand(3, 60))->toDateString(),
                'time_slot'        => ['08:00', '09:00', '10:00'][$i % 3],
                'location_address' => 'Jl. Raya Indramayu No. ' . (10 + $i) . ', Indramayu',
                'price'            => $service->price,
                'status'           => $status,
                'verification_code'=> $status === 'pending' ? null : $code,
                'notes'            => 'Mohon datang tepat waktu ya kak.',
                'created_at'       => now()->subDays(rand(1, 10)),
                'updated_at'       => now(),
            ]);

            // Tambahkan review kalau status completed
            if ($status === 'completed') {
                $rating = rand(4, 5);
                DB::table('reviews')->insert([
                    'booking_id' => $bookingId,
                    'user_id'    => $custId,
                    'mua_id'     => $mua->id,
                    'rating'     => $rating,
                    'comment'    => 'Pelayanan sangat memuaskan! MUA profesional dan hasil riasannya sangat bagus. Recommended!',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('');
        $this->command->info('✅ Database berhasil di-seed!');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  AKUN LOGIN:');
        $this->command->info('  Admin    → admin@beautyhub.com / password');
        $this->command->info('  MUA      → iah@beautyhub.com   / password');
        $this->command->info('  Customer → rina@gmail.com      / password');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🌐 Jalankan: php artisan serve');
        $this->command->info('   Buka: http://127.0.0.1:8000');
    }

    private function insertServices(int $muaId): void
    {
        $serviceList = [
            ['name' => 'Makeup Wedding',    'category' => 'wedding',    'price' => 1500000, 'desc' => 'Rias pengantin adat dan modern, tahan lama sepanjang hari acara.'],
            ['name' => 'Makeup Wisuda',     'category' => 'graduation', 'price' => 350000,  'desc' => 'Makeup fresh dan natural untuk momen wisuda yang spesial.'],
            ['name' => 'Makeup Party',      'category' => 'party',      'price' => 400000,  'desc' => 'Makeup glam dan elegan untuk pesta dan acara malam hari.'],
            ['name' => 'Makeup Photoshoot', 'category' => 'photoshoot', 'price' => 500000,  'desc' => 'Makeup profesional untuk sesi foto studio maupun outdoor.'],
            ['name' => 'Makeup Formal',     'category' => 'formal',     'price' => 300000,  'desc' => 'Makeup rapi dan profesional untuk acara resmi dan formal.'],
        ];

        foreach ($serviceList as $s) {
            DB::table('services')->insert([
                'mua_id'      => $muaId,
                'name'        => $s['name'],
                'category'    => $s['category'],
                'price'       => $s['price'],
                'description' => $s['desc'],
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}