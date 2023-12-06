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
        Schema::create('transaksi_layanan', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('user_id_servicer')->references('id')->on('user');
            $table->string('user_id_customer')->references('id')->on('user');
            $table->string('project_name');
            $table->unsignedBigInteger('offering_cost');
            $table->string('description');
            $table->string('customer_name');
            $table->string('storyboard')->nullable();
            $table->string('bukti_transaksi')->nullable();
            $table->boolean('is_deal')->default(0);
            $table->boolean('is_done')->default(0);
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('transaksi_layanan');
    }
};
