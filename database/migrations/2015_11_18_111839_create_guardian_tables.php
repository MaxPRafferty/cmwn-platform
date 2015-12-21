<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuardianTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::dropIfExists('child_guardian');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('guardian_reference');

/*        Schema::create('guardians', function (Blueprint $table) {
            $table->increments('id');
            //$table->string('uuid');
            $table->unsignedInteger('user_id')->unsigned();
            $table->string('student_id');
            //$table->foreign('student_id')->references('student_id')->on('users')->onDelete('cascade');
            //$table->unique(array('student_id', 'first_name', 'last_name', 'phone'));
            $table->timestamps();
            $table->softDeletes();
        });*/

        Schema::create('child_guardian', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('guardian_id')->unsigned();
            $table->unsignedInteger('child_id')->unsigned();
            //$table->foreign('child_id')->references('uuid')->on('users')->onDelete('cascade');
            //$table->foreign('guardian_id')->references('uuid')->on('users')->onDelete('cascade');
            //$table->unique(array('guardian_id', 'child_id'));
            $table->timestamps();
        });

        Schema::create('guardian_reference', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unsigned();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
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
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('child_guardian');
        Schema::dropIfExists('guardian_reference');
    }
}
