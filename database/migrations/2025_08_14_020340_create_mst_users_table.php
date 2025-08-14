<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mst_users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->unsignedBigInteger('pegawai_id');
            $table->tinyInteger('deleted_st')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('pegawai_id')->references('pegawai_id')->on('mst_pegawai')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('mst_users');
    }
};
