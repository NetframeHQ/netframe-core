<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TranslationController extends Controller
{

    /**
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        $data = [];



        return view('admin.translation.home', $data);
    }
}
