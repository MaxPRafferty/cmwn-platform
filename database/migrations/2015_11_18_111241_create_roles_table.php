<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('roleables');
        Schema::dropIfExists('roles');

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('roleables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('roleable_id');
            $table->string('roleable_type');
            $table->unsignedInteger('role_id')->unsigned();
            $table->unique(array('user_id', 'roleable_id','roleable_type'));
            //$table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('roleables');
        Schema::dropIfExists('roles');
    }
}
