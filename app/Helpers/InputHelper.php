<?php

namespace App\Helpers;

class InputHelper
{

    /**
     * InputHelper get for display value errors field without use
     * Validator session Laravel, if your form we can't using request()->old()
     *
     * @param unknown $fieldName
     * @return Ambigous <>|NULL
     */
    public static function get($fieldName)
    {
        $input = request()->all();

        if (isset($input[$fieldName]) and !empty($input[$fieldName])) {
            return $input[$fieldName];
        } else {
            return null;
        }
    }
}
