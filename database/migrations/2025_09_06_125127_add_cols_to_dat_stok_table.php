<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('dat_stok', function (Blueprint $table) {
           
            $table->enum('satuan',['tablet','strip'])->nullable()->after('keluar'); // opsional
           
        });
    }
    public function down(): void {
        Schema::table('dat_stok', function (Blueprint $table) {
            $table->dropColumn(['satuan']);
        });
    }
};
