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
            $table->integer('jumlah_tiket')->default(1)->after('event_id');
            $table->decimal('total_harga', 15, 2)->default(0)->after('jumlah_tiket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_event', function (Blueprint $table) {
            //
        });
    }
};
