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
        Schema::table('kontak_event', function (Blueprint $table) {
            $table->text('balasan')->nullable()->after('pesan');
            $table->timestamp('replied_at')->nullable()->after('balasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontak_event', function (Blueprint $table) {
            $table->dropColumn(['balasan', 'replied_at']);
        });
    }
};
