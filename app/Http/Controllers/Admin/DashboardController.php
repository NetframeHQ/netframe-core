<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [];

        return view('admin.dashboard.home', $data);
    }
}
