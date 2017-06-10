<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContestsProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contests_problems', function (Blueprint $table) {
            $table->bigInteger('contest_id');
            $table->bigInteger('problem_id');
            $table->string('title');
            $table->string('color')->nullable();
            $table->bigInteger('ac_num')->default(0);
            $table->bigInteger('submit_num')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contests_problems');
    }
}
