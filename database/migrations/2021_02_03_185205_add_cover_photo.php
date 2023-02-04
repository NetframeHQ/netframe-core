<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCoverPhoto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add cover_media_id to profiles tables
        if (!Schema::hasColumn('users', 'cover_media_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->bigInteger('cover_media_id')->after('profile_media_id')->nullable();
            });
        }

        if (!Schema::hasColumn('community', 'cover_media_id')) {
            Schema::table('community', function (Blueprint $table) {
                $table->bigInteger('cover_media_id')->after('profile_media_id')->nullable();
            });
        }

        if (!Schema::hasColumn('houses', 'cover_media_id')) {
            Schema::table('houses', function (Blueprint $table) {
                $table->bigInteger('cover_media_id')->after('profile_media_id')->nullable();
            });
        }

        if (!Schema::hasColumn('projects', 'cover_media_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->bigInteger('cover_media_id')->after('profile_media_id')->nullable();
            });
        }

        // add cover_media boolean in pivot table between profiles and medias
        if (!Schema::hasColumn('users_has_medias', 'cover_media_id')) {
            Schema::table('users_has_medias', function (Blueprint $table) {
                $table->tinyInteger('cover_image')->after('profile_image')->nullable(false)->default(0);
            });
        }

        if (!Schema::hasColumn('community_has_medias', 'cover_media_id')) {
            Schema::table('community_has_medias', function (Blueprint $table) {
                $table->tinyInteger('cover_image')->after('profile_image')->nullable(false)->default(0);
            });
        }

        if (!Schema::hasColumn('houses_has_medias', 'cover_media_id')) {
            Schema::table('houses_has_medias', function (Blueprint $table) {
                $table->tinyInteger('cover_image')->after('profile_image')->nullable(false)->default(0);
            });
        }

        if (!Schema::hasColumn('projects_has_medias', 'cover_media_id')) {
            Schema::table('projects_has_medias', function (Blueprint $table) {
                $table->tinyInteger('cover_image')->after('profile_image')->nullable(false)->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
