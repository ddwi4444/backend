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
        Schema::create('review_layanan', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 64)->unique();
            $table->unsignedBigInteger('transaksi_layanan_id')->references('id')->on('transaksi_layanan');
            $table->string('user_id_servicer', 50)->references('id')->on('user');
            $table->string('user_id_customer', 50)->references('id')->on('user');
            $table->string('post_by');
            $table->string('isi');
            $table->integer('rating');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('transaksi_layanan_id')->references('id')->on('transaksi_layanan')->onDelete('cascade');
            $table->foreign('user_id_servicer')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('user_id_customer')->references('id')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_layanan');
    }
};
