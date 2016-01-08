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
            $table->integer('imageable_id');
            $table->string('imageable_type');
            $table->string('url');
            $table->string('cloudinary_id');
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
