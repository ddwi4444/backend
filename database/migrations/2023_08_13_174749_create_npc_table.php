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
        Schema::create('npc', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 50)->references('id')->on('user');
            $table->string('npc_name');
            $table->mediumText('npc_profile');
            $table->string('nama_author'); //menggunakan nama persona
            $table->mediumText('npc_story');
            $table->string('image_npc');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('npc');
    }
};
