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
        Schema::create('like_sub_comic', function (Blueprint $table) {
            $table->id();
            $table->string('subKomik_uuid')->references('uuid')->on('sub_komik');
            $table->string('user_uuid')->references('uuid')->on('user');

            $table->foreign('user_uuid')->references('uuid')->on('user')->onDelete('cascade');
            $table->foreign('subKomik_uuid')->references('uuid')->on('sub_komik')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('like_sub_comic');
    }
};
