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
        Schema::create('komen', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 50)->references('id')->on('user');
            $table->unsignedBiginteger('sub_komik_id')->references('id')->on('sub_komik');
            $table->unsignedBiginteger('komen_parent_id')->references('id')->on('komen')->nullable();
            $table->string('isi');
            $table->string('komen_by');
            $table->boolean('status')->default(1);
            $table->boolean('is_reported')->default(0);
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('sub_komik_id')->references('id')->on('sub_komik')->onDelete('cascade');
            $table->foreign('komen_parent_id')->references('id')->on('komen')->onDelete('cascade');
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
        Schema::dropIfExists('komen');
    }
};
