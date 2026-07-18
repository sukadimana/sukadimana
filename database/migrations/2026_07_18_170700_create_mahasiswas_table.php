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
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->foreignId('prodi_id')->constrained('prodis');
            $table->string('nim')->unique();
            $table->string('nama_lengkap');
            $table->string('angkatan');
            $table->string('tahun_masuk')->nullable();
            $table->string('status_akademik')->default('AKTIF');
            $table->string('kategori_beasiswa')->default('NONE');
            $table->decimal('beasiswa_potongan', 15, 2)->default(0);
            $table->string('jenjang')->default('S1');
            $table->unsignedTinyInteger('semester_saat_ini')->default(1);
            $table->string('jenis_kelamin', 1)->nullable();
            $table->foreignId('tahun_akademik_id_saat_ini')->nullable()->constrained('tahun_akademiks')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
