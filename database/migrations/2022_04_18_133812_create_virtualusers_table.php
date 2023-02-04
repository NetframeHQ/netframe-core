<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVirtualusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('instances_id')->unsigned();
            $table->bigInteger('users_id')->unsigned();
            $table->string('firstname', '45');
            $table->string('lastname', '45');
            $table->string('email', '250');
            $table->tinyInteger('active');
            $table->string('password', '64');
            $table->string('password_token', '255');
            $table->dateTime('password_timeout');
            $table->string('remember_token', '255');
            $table->timestamps();
        });

        Schema::table('virtual_users', function ($table) {
            $table->foreign('instances_id', 'fk_virtual_users_instances1')->references('id')->on('instances');
            $table->foreign('users_id', 'fk_virtual_users_users1')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('virtual_users');
    }
}
