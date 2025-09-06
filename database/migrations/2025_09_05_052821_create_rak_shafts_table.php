<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('rak_shafts', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('rak_id');
        $table->string('nama_shaft', 100);
        $table->timestamps();

        $table->foreign('rak_id')->references('id')->on('raks')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('rak_shafts');
}

};
