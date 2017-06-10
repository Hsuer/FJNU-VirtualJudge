<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status', function (Blueprint $table) {
            $table->increments('id'); 
            $table->bigInteger('user_id');
            $table->bigInteger('problem_id');
            $table->bigInteger('contest_id')->nullable();
            $table->string('result');
            $table->integer('language');
            $table->bigInteger('time');
            $table->bigInteger('memory');
            $table->bigInteger('length');
            $table->boolean('is_public')->default(0);
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
        Schema::dropIfExists('status');
    }
}
