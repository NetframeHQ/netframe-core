<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

class PageController extends BaseController
{

    public function cgu()
    {
        return view('static.cgu');
    }

    public function cgv()
    {
        return view('static.cgv');
    }

    public function faq()
    {
        return view('static.faq');
    }

    public function contacts()
    {
        return view('static.contacts');
    }

    public function sampleProfile($profileType)
    {
        $data = array();
        $data['image'] = $profileType.'-'.Lang::locale().'.png';

        return view('static.modal-image', $data);
    }

    public function welcome($step)
    {
        return view('static.after-register', ['step' => $step]);
    }

    public function instanceClosed()
    {
        return view('static.instance-closed');
    }
}
