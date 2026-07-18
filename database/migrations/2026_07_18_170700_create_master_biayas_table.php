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
        Schema::create('master_biayas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_biaya');
            $table->string('angkatan');
            $table->foreignId('prodi_id')->nullable()->constrained('prodis')->nullOnDelete();
            $table->decimal('nominal_baku', 15, 2)->default(0);
            $table->boolean('bisa_dicicil')->default(true);
            $table->unsignedTinyInteger('semester')->nullable();
            $table->string('jenjang')->default('SEMUA');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_biayas');
    }
};
