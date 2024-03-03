<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('instances_id')->unsigned();
            $table->morphs('entity');
            $table->enum('stat_type', [
                'post',
                'likes',
                'comments',
                'shares',
                'users',
                'medias',
                'groups',
                'connexions'
            ]);
            $table->bigInteger('counter');
            $table->json('stat_detail');
            $table->date('day');
            $table->timestamps();
        });

        Schema::table('stats', function ($table) {
            $table->foreign('instances_id', 'fk_virtual_users_instances1')->references('id')->on('instances');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stats');
    }
};
