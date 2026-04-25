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
        Schema::table('pendaftaran_event', function (Blueprint $table) {
            $table->string('sertifikat_url')->nullable()->comment('Path file PDF sertifikat yang sudah digenerate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_event', function (Blueprint $table) {
            $table->dropColumn('sertifikat_url');
        });
    }
};
