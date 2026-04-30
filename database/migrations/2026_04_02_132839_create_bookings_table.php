<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mua_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
            $table->date('booking_date');
            $table->date('event_date');
            $table->string('time_slot', 20)->nullable();
            $table->text('location_address');
            $table->text('location_notes')->nullable();
            $table->decimal('price', 12, 0)->default(0);
            $table->text('notes')->nullable();
            $table->string('reference_image')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'verified', 'completed', 'cancelled'])->default('pending');
            $table->string('verification_code', 10)->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
