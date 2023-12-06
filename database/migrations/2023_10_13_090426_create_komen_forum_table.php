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
        Schema::create('komen_forum', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('user_id')->references('id')->on('user');
            $table->unsignedBiginteger('forum_id')->references('id')->on('forum');
            $table->unsignedBiginteger('komenForum_parent_id')->references('id')->on('komen_forum')->nullable();
            $table->string('isi');
            $table->string('komen_by');
            $table->boolean('is_reported')->default(0);
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('forum_id')->references('id')->on('forum')->onDelete('cascade');
            $table->foreign('komenForum_parent_id')->references('id')->on('komen_forum')->onDelete('cascade');
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
        Schema::dropIfExists('komen_forum');
    }
};
