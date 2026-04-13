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
        Schema::table('perkembangan', function (Blueprint $table) {
            $table->integer('bulan')->nullable()->after('nomor_induk_siswa');
            $table->integer('tahun')->nullable()->after('bulan');
            $table->integer('bb_count')->default(0)->after('tahun')->comment('Belum Berkembang');
            $table->integer('mb_count')->default(0)->after('bb_count')->comment('Mulai Berkembang');
            $table->integer('bsh_count')->default(0)->after('mb_count')->comment('Berkembang Sesuai Harapan');
            $table->integer('bsb_count')->default(0)->after('bsh_count')->comment('Berkembang Sangat Baik');
            $table->enum('status_utama', ['BB', 'MB', 'BSH', 'BSB'])->nullable()->after('bsb_count')->comment('Status utama pencapaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perkembangan', function (Blueprint $table) {
            $table->dropColumn(['bulan', 'tahun', 'bb_count', 'mb_count', 'bsh_count', 'bsb_count', 'status_utama']);
        });
    }
};
