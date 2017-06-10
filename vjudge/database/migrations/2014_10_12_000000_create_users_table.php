<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('nick');
            $table->string('student_id');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('school')->nullable();
            $table->string('description')->nullable();
            $table->bigInteger('ac')->default(0);
            $table->bigInteger('wa')->default(0);
            $table->bigInteger('ce')->default(0);
            $table->bigInteger('pe')->default(0);
            $table->bigInteger('re')->default(0);
            $table->bigInteger('tle')->default(0);
            $table->bigInteger('mle')->default(0);
            $table->bigInteger('ole')->default(0);
            $table->bigInteger('other')->default(0);
            $table->bigInteger('solve')->default(0);
            $table->bigInteger('submit')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
