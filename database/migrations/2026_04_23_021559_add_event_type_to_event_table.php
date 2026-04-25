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
            $table->string('event_type')->default('offline')->after('nama_event');
            $table->string('meeting_link')->nullable()->after('lokasi');
            // Change lokasi to nullable
            $table->string('lokasi')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->dropColumn(['event_type', 'meeting_link']);
            // Revert lokasi to required (assuming it was a string and not nullable)
            // It's safer to just make it nullable in down as well, or not change it back,
            // but typical rollback would enforce the string without nullable.
            $table->string('lokasi')->nullable(false)->change();
        });
    }
};
