<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('mst_barang', function (Blueprint $table) {
            $table->unsignedBigInteger('rak_id')->nullable()->after('barang_nm');
            $table->unsignedBigInteger('rak_shaft_id')->nullable()->after('rak_id');

            $table->foreign('rak_id')
                ->references('id')
                ->on('raks')
                ->onDelete('set null');

            $table->foreign('rak_shaft_id')
                ->references('id')
                ->on('rak_shafts')
                ->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::table('mst_barang', function (Blueprint $table) {
            $table->dropForeign(['rak_id']);
            $table->dropForeign(['rak_shaft_id']);
            $table->dropColumn(['rak_id', 'rak_shaft_id']);
        });
    }
};
