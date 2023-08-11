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
        Schema::create('user', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('uuid', 32)->unique();
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('firstname', 100);
            $table->string('lastname', 100)->nullable();
	        $table->string('image')->nullable();
	        $table->boolean('is_verified')->default(0);
            $table->tinyInteger('umur')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('zodiak')->nullable();
            $table->tinyInteger('tinggi_badan')->nullable();
            $table->tinyInteger('berat_badan')->nullable();
            $table->string('MBTI')->nullable();
	        $table->string('hobi')->nullable();
            $table->string('like')->nullable();
            $table->string('did_not_like')->nullable();
            $table->string('quotes')->nullable();
            $table->string('story_character')->nullable();
            $table->string('role')->default('user');
            $table->boolean('is_active')->default(1);
            $table->boolean('is_servicer')->default(0);
            $table->string('deskripsi_servicer', 100)->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('user');
    }
};
