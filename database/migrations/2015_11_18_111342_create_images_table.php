<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::dropIfExists('images');

        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->unique();
            $table->unsignedInteger('imageable_id')->unsigned();
            $table->string('imageable_type');
            $table->string('url');
            $table->unsignedInteger('cloudinary_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
