<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('dat_stok', function (Blueprint $table) {
    $table->id(); // id auto-increment sebagai primary key
    $table->unsignedBigInteger('barang_id');
    $table->integer('stok')->default(0);
    $table->date('exp_tgl')->nullable();
    $table->date('catat_tgl')->nullable();
    $table->boolean('deleted_st')->default(false);
    $table->timestamps();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->unsignedBigInteger('updated_by')->nullable();

    // Relasi ke mst_barang
    $table->foreign('barang_id')
          ->references('id')
          ->on('mst_barang')
          ->onDelete('cascade');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('dat_stok');
    }
};
