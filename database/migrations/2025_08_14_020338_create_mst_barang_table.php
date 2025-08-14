<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mst_barang', function (Blueprint $table) {
            $table->id();
            $table->string('barang_nm', 255);
            $table->tinyInteger('deleted_st')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('mst_barang');
    }
};
