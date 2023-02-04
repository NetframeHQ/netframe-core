<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{

    protected $table = "ref_countries";


    protected $fillable = ['iso', 'lang', 'name'];


    public static function getTableName()
    {
        return with(new static)->getTable();
    }


    public static function listFromLocale()
    {
        $query = \DB::table(static::getTableName());

        $arrayAssoc = array();

        $query->select(['iso', 'name'])
              ->where('lang', '=', \Lang::getLocale())
              ->orderBy('name', 'asc');

        foreach ($query->get() as $row) {
            $arrayAssoc[$row->iso] = $row->name;
        }

        return $arrayAssoc;
    }

    public static function listIdFromLocale()
    {
        $query = \DB::table(static::getTableName());

        $arrayAssoc = array();

        $query->select(['id', 'name'])
        ->where('lang', '=', \Lang::getLocale())
        ->orderBy('name', 'asc');

        foreach ($query->get() as $row) {
            $arrayAssoc[$row->id] = $row->name;
        }

        return $arrayAssoc;
    }
}
