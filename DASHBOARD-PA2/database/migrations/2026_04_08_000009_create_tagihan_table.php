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
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id('id_tagihan');
            $table->string('nomor_induk_siswa', 20);
            $table->decimal('jumlah_tagihan', 10, 2);
            $table->string('periode', 20);
            $table->enum('status', ['belum_bayar', 'lunas']);
            $table->timestamps();
            
            $table->foreign('nomor_induk_siswa')->references('nomor_induk_siswa')->on('siswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
