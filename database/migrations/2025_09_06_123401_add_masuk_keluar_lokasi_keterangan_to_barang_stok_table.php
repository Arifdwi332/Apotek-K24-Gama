<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dat_stok', function (Blueprint $table) {
            // jumlah masuk & keluar per catatan stok
            $table->integer('masuk')->unsigned()->nullable()->after('stok');     // atau after('catat_tgl') sesuaikan
            $table->integer('keluar')->unsigned()->nullable()->after('masuk');

            // lokasi & keterangan tambahan
            $table->string('lokasi', 150)->nullable()->after('keluar');
            $table->text('keterangan')->nullable()->after('lokasi');
        });

        // Opsional: backfill dari kolom stok lama jika tanda +/-
        // asumsikan 'stok' menyimpan delta (+ untuk masuk, - untuk keluar)
        DB::statement("
            UPDATE dat_stok
            SET masuk  = CASE WHEN stok > 0 THEN stok ELSE 0 END,
                keluar = CASE WHEN stok < 0 THEN ABS(stok) ELSE 0 END
        ");
    }

    public function down(): void
    {
        Schema::table('dat_stok', function (Blueprint $table) {
            $table->dropColumn(['masuk', 'keluar', 'lokasi', 'keterangan']);
        });
    }
};
