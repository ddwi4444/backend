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
        Schema::create('order_produk_merchandise', function (Blueprint $table) {
            $table->id();
            $table->string('uuidOrderMerchandise')->references('uuid')->on('order_merchandise');
            $table->string('namaProduk');
            $table->string('UUIDProduk');
            $table->integer('quantity');
            $table->string('notes');

            $table->timestamps();
            $table->foreign('uuidOrderMerchandise')->references('uuid')->on('order_merchandise')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_order_produk_merchandise');
    }
};
