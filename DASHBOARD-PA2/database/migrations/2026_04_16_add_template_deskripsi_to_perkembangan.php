<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perkembangan', function (Blueprint $table) {
            $table->text('template_deskripsi')->nullable()->after('deskripsi')->comment('Template deskripsi otomatis berdasarkan status');
        });
    }

    public function down(): void
    {
        Schema::table('perkembangan', function (Blueprint $table) {
            $table->dropColumn('template_deskripsi');
        });
    }
};
