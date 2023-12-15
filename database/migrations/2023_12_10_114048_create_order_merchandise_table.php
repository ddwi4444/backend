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
        Schema::create('order_merchandise', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('order_by');
            $table->string('user_id')->references('id')->on('user');
            $table->string('nama');
            $table->string('alamat');
            $table->string('tlp');
            $table->decimal('total_prices', 10, 2);
            $table->string('buktiTf')->nullable();
            $table->string('noResi')->nullable();
            $table->integer('status');
            $table->softDeletes();

            $table->timestamps();
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
        Schema::dropIfExists('_order_merchandise');
    }
};
