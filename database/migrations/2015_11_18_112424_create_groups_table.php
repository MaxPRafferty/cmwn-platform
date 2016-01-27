<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::dropIfExists('groups');

        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->unique();
            $table->string('organization_id');
            $table->string('code');
            $table->string('title');
            $table->string('class_number')->nullable();
            $table->string('cluster_class')->nullable();
            $table->unique(array('organization_id', 'uuid'));
            $table->string('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
