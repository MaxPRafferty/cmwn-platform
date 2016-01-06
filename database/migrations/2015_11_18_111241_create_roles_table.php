<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::dropIfExists('roleables');

        Schema::create('roleables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('roleable_id')->unsigned();
            $table->string('roleable_type');
            $table->unsignedInteger('role_id')->unsigned();
            $table->unique(array('user_id', 'roleable_id', 'roleable_type'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('roleables');
    }
}
