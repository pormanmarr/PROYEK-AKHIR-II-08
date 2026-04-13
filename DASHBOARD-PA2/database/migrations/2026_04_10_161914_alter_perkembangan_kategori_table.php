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
        Schema::table('perkembangan_kategori', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['status_utama', 'deskripsi']);
        });
        
        Schema::table('perkembangan_kategori', function (Blueprint $table) {
            // Add new columns
            $table->integer('nilai')->between(1, 10)->after('nama_kategori')->comment('Nilai perkembangan 1-10');
            $table->text('deskripsi')->after('nilai')->nullable()->comment('Deskripsi auto-generated');
            $table->enum('status_utama', ['BB', 'MB', 'BSH', 'BSB'])->after('deskripsi')->comment('Status capaian global');
        });
    }

    public function down(): void
    {
        Schema::table('perkembangan_kategori', function (Blueprint $table) {
            $table->dropColumn(['nilai', 'deskripsi', 'status_utama']);
            $table->enum('status_utama', ['BB', 'MB', 'BSH', 'BSB'])->nullable();
            $table->text('deskripsi');
        });
    }
};
