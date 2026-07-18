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
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('master_biaya_id')->constrained('master_biayas');
            $table->decimal('nominal_awal', 15, 2)->default(0);
            $table->decimal('potongan_beasiswa', 15, 2)->default(0);
            $table->decimal('total_harus_bayar', 15, 2)->default(0);
            $table->decimal('total_sudah_bayar', 15, 2)->default(0);
            $table->decimal('sisa_tagihan', 15, 2)->default(0);
            $table->string('status')->default('BELUM_LUNAS');
            $table->string('nama_tagihan_snapshot')->nullable();
            $table->string('nama_mahasiswa_snapshot')->nullable();
            $table->unsignedTinyInteger('semester')->nullable();
            $table->string('kategori_snapshot')->nullable();
            $table->date('jatuh_tempo')->nullable();
            $table->foreignId('tahun_akademik_id_snapshot')->nullable()->constrained('tahun_akademiks')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
