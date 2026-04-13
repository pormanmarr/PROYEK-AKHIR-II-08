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
        Schema::create('akun', function (Blueprint $table) {
            $table->id('id_akun');
            $table->unsignedBigInteger('id_guru')->nullable();
            $table->string('nomor_induk_siswa', 20)->nullable();
            $table->string('username', 50);
            $table->string('password', 100);
            $table->enum('role', ['guru', 'orangtua']);
            $table->timestamps();
            
            $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('cascade');
            $table->foreign('nomor_induk_siswa')->references('nomor_induk_siswa')->on('siswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akun');
    }
};
