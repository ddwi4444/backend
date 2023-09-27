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
        Schema::create('images_merchandise', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchandise_id')->references('id')->on('merchandise');
            $table->string('images_merchandise_path');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('merchandise_id')->references('id')->on('merchandise')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images_merchandise');
    }
};
