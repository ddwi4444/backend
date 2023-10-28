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
        Schema::create('images_komen_forum', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('komenForum_id')->references('id')->on('komen_forum');
            $table->string('images_komenForum_path');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('komenForum_id')->references('id')->on('komen_forum')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images_komen_forum');
    }
};
