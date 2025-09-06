<?php

// database/migrations/2025_09_06_000010_create_mst_barang_shaft_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mst_barang_shaft', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id');
            $table->unsignedBigInteger('rak_shaft_id');
            $table->timestamps();

            $table->foreign('barang_id')->references('id')->on('mst_barang')->onDelete('cascade');
            $table->foreign('rak_shaft_id')->references('id')->on('rak_shafts')->onDelete('cascade');
            $table->unique(['barang_id','rak_shaft_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('mst_barang_shaft');
    }
};
