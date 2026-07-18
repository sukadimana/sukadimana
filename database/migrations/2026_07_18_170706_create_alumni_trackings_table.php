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
        Schema::create('alumni_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->string('tahun_lulus');
            $table->string('nomor_hp_terbaru')->nullable();
            $table->text('alamat_domisili')->nullable();
            $table->string('status_pekerjaan')->default('BELUM_KERJA');
            $table->string('kategori_pekerjaan')->nullable();
            $table->string('nama_instansi')->nullable();
            $table->string('jurusan_lanjutan')->nullable();
            $table->string('prodi_lanjutan')->nullable();
            $table->string('jabatan')->nullable();
            $table->decimal('gaji_pertama', 15, 2)->nullable();
            $table->date('tanggal_isi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_trackings');
    }
};
