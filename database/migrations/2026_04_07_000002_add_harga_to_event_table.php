<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event', function (Blueprint $table) {
            if (!Schema::hasColumn('event', 'harga')) {
                $table->decimal('harga', 12, 2)->default(0)->after('lokasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            if (Schema::hasColumn('event', 'harga')) {
                $table->dropColumn('harga');
            }
        });
    }
};
