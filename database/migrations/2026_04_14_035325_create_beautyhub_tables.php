<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * BeautyHub — Single migration file untuk semua tabel.
 * 
 * Cara pakai:
 * 1. Hapus semua file migration lain di database/migrations/ KECUALI file ini
 * 2. Jalankan: php artisan migrate
 * 
 * Atau kalau mau fresh start:
 *    php artisan migrate:fresh --seed
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. USERS ──────────────────────────────────────────────
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'mua', 'customer'])->default('customer');
            $table->string('phone', 20)->nullable();
            $table->string('avatar')->nullable();
            $table->string('address')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // ── 2. MUAS ───────────────────────────────────────────────
        Schema::create('muas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('location')->nullable();
            $table->text('bio')->nullable();
            $table->integer('experience_years')->default(0);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->json('style_tags')->nullable();
            $table->string('certificate')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });

        // ── 3. SERVICES ───────────────────────────────────────────
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mua_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 0)->default(0);
            $table->enum('category', ['wedding', 'graduation', 'party', 'photoshoot', 'formal', 'other'])->default('other');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── 4. PORTFOLIOS ─────────────────────────────────────────
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mua_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('title')->nullable();
            $table->text('caption')->nullable();
            $table->json('feature_vector')->nullable();
            $table->enum('style_category', [
                'wedding', 'graduation', 'party', 'photoshoot',
                'formal', 'natural', 'glam', 'other'
            ])->nullable();
            $table->timestamps();
        });

        // ── 5. BOOKINGS ───────────────────────────────────────────
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mua_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->date('booking_date');
            $table->date('event_date');
            $table->string('time_slot', 20)->nullable();
            $table->text('location_address');
            $table->text('location_notes')->nullable();
            $table->decimal('price', 12, 0)->default(0);
            $table->text('notes')->nullable();
            $table->string('reference_image')->nullable();
            $table->enum('status', [
                'pending', 'approved', 'rejected',
                'verified', 'completed', 'cancelled'
            ])->default('pending');
            $table->string('verification_code', 10)->nullable()->unique();
            $table->string('qr_code_path')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // ── 6. REVIEWS ────────────────────────────────────────────
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mua_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->default(5);
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // ── 7. CHATBOT LOGS ───────────────────────────────────────
        Schema::create('chatbot_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('user_message');
            $table->string('detected_intent')->nullable();
            $table->text('bot_response');
            $table->json('parameters')->nullable();
            $table->timestamps();
        });

        // ── 8. PERSONAL ACCESS TOKENS (untuk JWT fallback) ───────
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_logs');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('services');
        Schema::dropIfExists('muas');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('users');
    }
};