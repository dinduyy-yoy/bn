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
        Schema::table('event', function (Blueprint $table) {
            $table->string('sertifikat_template')->nullable()->comment('Path file gambar template sertifikat (PNG/JPG)');
            $table->json('sertifikat_config')->nullable()->comment('Konfigurasi posisi teks sertifikat (x, y, font_size, dll)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->dropColumn(['sertifikat_template', 'sertifikat_config']);
        });
    }
};
