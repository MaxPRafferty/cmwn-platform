<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddComingSoonToGamesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->integer('coming_soon')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('coming_soon');
        });
    }
}
