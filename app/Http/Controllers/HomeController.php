<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

class HomeController extends BaseController
{
    public function showHome()
    {
        return resolve('App\Http\Controllers\MosaicController')->showMosaicByCat(0);
    }
}
