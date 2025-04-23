<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_penjualan_detail', function (Blueprint $table) {
            // Drop foreign key lama
            $table->dropForeign(['penjualan_id']);

            // Tambahkan foreign key baru dengan cascade
            $table->foreign('penjualan_id')
                  ->references('penjualan_id')->on('t_penjualan')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('t_penjualan_detail', function (Blueprint $table) {
            // Balik ke versi tanpa cascade
            $table->dropForeign(['penjualan_id']);
            $table->foreign('penjualan_id')
                  ->references('penjualan_id')->on('t_penjualan');
        });
    }
};
