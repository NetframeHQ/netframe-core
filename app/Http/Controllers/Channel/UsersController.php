<?php
namespace App\Http\Controllers\Channel;

use App\Http\Controllers\BaseController;
use \App\Helpers\Lib\Acl;
use App\Channel;

class UsersController extends BaseController
{
    public function __construct()
    {
        $this->middleware('checkAuth');

        //add control for channel manager
    }

    /**
     * @TODO check if do it in joincontroller
     */
    public function invite()
    {
    }
}
