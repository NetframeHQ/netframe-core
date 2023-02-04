<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{

    protected $table = "ref_langs";

    protected $fillable = [
        'iso_639_1',
        'iso_639_2',
        'lang',
        'name'
    ];

    public static function getTableName()
    {
        return with(new static())->getTable();
    }


    /**
     * Get local language
     *
     * @return object query
     */
    public static function getLang()
    {
        $query = \DB::table(self::getTableName());

        $query->where('iso_639_1', '=', config('app.locale'))
              ->where('lang', '=', config('app.locale'));

        return $query->get();
    }


    /**
     * Return List language formated for select
     *
     * @return array
     */
    public static function listLang()
    {
        $arrayAssoc = array();
        $query = \DB::table(self::getTableName());

        $query->select('iso_639_1', 'lang', 'name')
              ->where('lang', '=', \Lang::getLocale())
              ->where('iso_639_1', '<>', '')
              ->orderBy('name', 'ASC');

        foreach ($query->get() as $row) {
            $arrayAssoc[$row->iso_639_1] = $row->name;
        }

        return $arrayAssoc;
    }

    /**
     * Return List language formated for select with iso_639_2
     *
     * @return array
     */
    public static function listLang6392()
    {
        $arrayAssoc = array();
        $query = \DB::table(self::getTableName());

        $query->select('iso_639_2', 'lang', 'name')
        ->where('lang', '=', \Lang::getLocale())
        ->where('iso_639_2', '<>', '')
        ->orderBy('name', 'ASC');

        foreach ($query->get() as $row) {
            $arrayAssoc[$row->iso_639_2] = $row->name;
        }

        return $arrayAssoc;
    }


    /**
     * Get array formatted lang active enables lang netframe for select
     *
     * @return array language enable
     */
    public static function listLangNetframe()
    {
        $arrayAssoc = array();
        $query = \DB::table(self::getTableName());

        $query->select('id', 'iso_639_1', 'lang', 'name')
        ->where('lang', '=', \Lang::getLocale())
        ->where('iso_639_1', '<>', '')
        ->where('active', '=', true)
        ->orderBy('name', 'ASC');

        foreach ($query->get() as $row) {
            $arrayAssoc[$row->iso_639_1] = $row->name;
        }

        return $arrayAssoc;
    }
}
