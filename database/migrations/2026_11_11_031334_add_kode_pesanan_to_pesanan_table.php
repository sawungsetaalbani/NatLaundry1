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
        if (! Schema::hasColumn('pesanan', 'kode_pesanan')) {
            Schema::table('pesanan', function (Blueprint $table) {
                // biarkan definisi kolom aslimu di sini, contoh:
                $table->string('kode_pesanan', 8)->nullable()->unique()->after('id_pesanan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pesanan', 'kode_pesanan')) {
            Schema::table('pesanan', function (Blueprint $table) {
                $table->dropColumn('kode_pesanan');
            });
        }
    }
};
