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
            $table->string('id')->primary();
            $table->string('uuid', 64)->unique();
            $table->string('email')->unique();
            $table->string('nama_persona')->unique();
            $table->string('bio')->nullable();
            $table->string('password');
            $table->string('nama_OC', 100)->nullable();
            $table->string('no_tlp', 100)->nullable();
	        $table->string('image')->nullable();
            $table->string('ig_acc')->nullable();
	        $table->boolean('is_verified')->default(0);
            $table->string('umur')->nullable();
            $table->string('umur_rl')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->date('tanggal_lahir_rl')->nullable();
            $table->string('ras')->nullable();
            $table->string('zodiak')->nullable();
            $table->string('tinggi_badan')->nullable();
            $table->string('berat_badan')->nullable();
            $table->string('MBTI')->nullable();
	        $table->string('hobi')->nullable();
            $table->string('like')->nullable();
            $table->string('did_not_like')->nullable();
            $table->string('quotes')->nullable();
            $table->mediumText('story_character')->nullable();
            $table->string('eskul')->nullable();
            $table->string('role')->default('user');
            $table->boolean('is_active')->default(1);
            $table->bigInteger('projects')->nullable();
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
