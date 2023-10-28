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
        Schema::create('images_forum', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_id')->references('id')->on('forum');
            $table->string('images_forum_path');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('forum_id')->references('id')->on('forum')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images_forum');
    }
};
