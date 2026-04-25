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
            $table->json('custom_form_schema')->nullable()->after('harga');
        });

        Schema::table('pendaftaran_event', function (Blueprint $table) {
            $table->json('custom_form_responses')->nullable()->after('status_pendaftaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->dropColumn('custom_form_schema');
        });

        Schema::table('pendaftaran_event', function (Blueprint $table) {
            $table->dropColumn('custom_form_responses');
        });
    }
};
