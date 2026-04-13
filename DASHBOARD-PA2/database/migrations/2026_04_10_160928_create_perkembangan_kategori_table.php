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
        Schema::create('perkembangan_kategori', function (Blueprint $table) {
            $table->id('id_perkembangan_kategori');
            $table->unsignedBigInteger('id_perkembangan');
            $table->enum('nama_kategori', ['Akademik', 'Sosial', 'Emosional']);
            $table->enum('status_utama', ['BB', 'MB', 'BSH', 'BSB']);
            $table->text('deskripsi');
            $table->timestamps();
            
            $table->foreign('id_perkembangan')->references('id_perkembangan')->on('perkembangan')->onDelete('cascade');
            $table->unique(['id_perkembangan', 'nama_kategori']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perkembangan_kategori');
    }
};
