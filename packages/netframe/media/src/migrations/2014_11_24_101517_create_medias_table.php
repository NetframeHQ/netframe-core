<?php

namespace Migration;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_alt')->nullable();
            $table->integer('type');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->dateTime('date');
            $table->string('mime_type')->nullable();
            $table->integer('confidentiality')->default(1);
            $table->string('platform');
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
        Schema::drop('medias');
    }
}
