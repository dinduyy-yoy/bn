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
            if (!Schema::hasColumn('event', 'metode_pembayaran')) {
                $table->string('metode_pembayaran')->nullable()->after('harga');
            }
            if (!Schema::hasColumn('event', 'detail_pembayaran')) {
                $table->text('detail_pembayaran')->nullable()->after('metode_pembayaran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            if (Schema::hasColumn('event', 'metode_pembayaran')) {
                $table->dropColumn('metode_pembayaran');
            }
            if (Schema::hasColumn('event', 'detail_pembayaran')) {
                $table->dropColumn('detail_pembayaran');
            }
        });
    }
};
