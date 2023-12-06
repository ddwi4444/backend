<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchandise', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('user_id')->references('id')->on('user');
            $table->string('nama');
            $table->string('deskripsi');
            $table->string('thumbnail');
            $table->string('harga');
            $table->string('stok');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchandise_models');
    }
};
