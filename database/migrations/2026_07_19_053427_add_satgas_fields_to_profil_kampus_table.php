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
        Schema::table('profil_kampus', function (Blueprint $table) {
            $table->string('satgas_nama')->nullable();
            $table->text('satgas_deskripsi')->nullable();
            $table->string('satgas_email')->nullable();
            $table->string('satgas_telepon')->nullable();
            $table->string('satgas_logo_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_kampus', function (Blueprint $table) {
            $table->dropColumn(['satgas_nama', 'satgas_deskripsi', 'satgas_email', 'satgas_telepon', 'satgas_logo_url']);
        });
    }
};
