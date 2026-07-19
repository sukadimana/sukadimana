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
        Schema::create('ppks_reports', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code')->unique();
            $table->string('reporter_name')->nullable();
            $table->string('reporter_contact')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->text('description');
            $table->string('status')->default('BARU');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppks_reports');
    }
};
