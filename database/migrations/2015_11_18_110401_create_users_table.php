<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::dropIfExists('game_user');
        Schema::dropIfExists('flip_user');
        Schema::dropIfExists('friends');
        Schema::dropIfExists('child_guardian');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('roleables');
        Schema::dropIfExists('users');
        
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->unique();
            $table->string('student_id')->unique();
            $table->string('username')->unique();
            $table->integer('type')->default(0);
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email');
            $table->char('gender', 6)->nullable();
            $table->date('birthdate');
            $table->string('password', 60);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
