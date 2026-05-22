<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel ini sudah ada di DB production — skip jika sudah ada
        if (!Schema::hasTable('beautyhub_tables')) {
            Schema::create('beautyhub_tables', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('beautyhub_tables');
    }
};
