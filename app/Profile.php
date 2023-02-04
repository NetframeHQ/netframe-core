<?php
namespace App;

use Illuminate\Support\Str;

class Profile
{
    const TYPE_USER = 'user';
    const TYPE_HOUSE = 'house';
    const TYPE_COMMUNITY = 'community';
    const TYPE_PROJECT = 'project';

    public static function user()
    {
        return new User();
    }

    public static function house()
    {
        return new House();
    }

    public static function channel()
    {
        return new Channel();
    }

    public static function community()
    {
        return new Community();
    }

    public static function project()
    {
        return new Project();
    }

    public static function users()
    {
        return new User();
    }

    public static function houses()
    {
        return new House();
    }

    public static function channels()
    {
        return new Channel();
    }

    public static function communities()
    {
        return new Community();
    }

    public static function projects()
    {
        return new Project();
    }


    public static function getTypes()
    {
        return array(
            self::TYPE_USER,
            self::TYPE_HOUSE,
            self::TYPE_COMMUNITY,
            self::TYPE_PROJECT
        );
    }


    /**
     * Gather different profile in called instance profile target
     * give singular lower case name of model
     *
     * @param string $modelName method name of profile
     * @return object
     */
    public static function gather($modelName)
    {
        $modelName = strtolower($modelName);
        return static::{$modelName}();
    }
}
