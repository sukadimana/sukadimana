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
        Schema::create('profil_kampus', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kampus');
            $table->text('alamat_kampus')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->longText('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->string('petugas_pembayaran')->nullable();
            $table->string('tahun_akademik')->nullable();
            $table->boolean('is_tahun_akademik_aktif')->default(false);
            $table->date('yudisium_start_date')->nullable();
            $table->date('yudisium_end_date')->nullable();
            $table->boolean('yudisium_is_open')->default(false);
            $table->string('wa_api_url')->nullable();
            $table->string('wa_api_key')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_kampus');
    }
};
