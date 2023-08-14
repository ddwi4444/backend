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
        Schema::create('komik', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 32)->references('id')->on('user');
            $table->string('judul');
            $table->string('genre');
            $table->string('thumbnail');
            $table->string('content');
            $table->tinyInteger('chapter')->nullable();
            $table->tinyInteger('volume')->nullable();
            $table->unsignedBiginteger('jumlah_view')->nullable();
	        $table->unsignedBiginteger('jumlah_like')->nullable();
            $table->string('nama_author')->nullable();
            $table->string('post_by');
            $table->string('instagram_author');
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('komik');
    }
};
