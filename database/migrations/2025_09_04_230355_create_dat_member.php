<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('dat_member', function (Blueprint $table) {
            $table->bigIncrements('id_member');
            $table->string('nama_lengkap');
            $table->string('no_hp', 30)->nullable();
            $table->enum('jenis_kelamin', ['L','P'])->nullable(); // L = Laki-laki, P = Perempuan
            $table->unsignedSmallInteger('usia')->nullable();
            $table->text('alamat')->nullable();

            // audit field
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps(); // created_at & updated_at
        });
    }
    public function down(): void {
        Schema::dropIfExists('dat_member');
    }
};
