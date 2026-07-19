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
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->cascadeOnDelete();
            $table->string('nilai_huruf', 2)->default('A');
            $table->decimal('bobot', 3, 2)->default(4);
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'mata_kuliah_id', 'tahun_akademik_id'], 'nilai_unique_per_semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};
