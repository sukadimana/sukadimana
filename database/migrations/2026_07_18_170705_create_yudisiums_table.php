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
        Schema::create('yudisiums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->string('bebas_pustaka')->default('PENDING');
            $table->text('catatan_pustaka')->nullable();
            $table->string('bebas_keuangan')->default('PENDING');
            $table->text('catatan_keuangan')->nullable();
            $table->string('bebas_akademik')->default('PENDING');
            $table->text('catatan_akademik')->nullable();
            $table->string('status_akhir')->default('BELUM');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yudisiums');
    }
};
