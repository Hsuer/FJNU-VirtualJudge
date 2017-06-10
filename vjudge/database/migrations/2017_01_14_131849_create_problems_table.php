<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

class CreateProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problems', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('origin_oj');
            $table->string('origin_id');
            $table->bigInteger('time');
            $table->bigInteger('memory');
            $table->boolean('special_judge');
            $table->text('description');
            $table->text('input');
            $table->text('output');
            $table->text('sample_input');
            $table->text('sample_output');
            $table->text('hint');
            $table->string('author');
            $table->string('source');
            $table->boolean('available');
            $table->bigInteger('father_id')->nullable();
            $table->bigInteger('ac_num')->default(0);
            $table->bigInteger('submit_num')->default(0);
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
        Schema::dropIfExists('problems');
        Cache::flush();
    }
}
