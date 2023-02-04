<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{

    public function login()
    {

        if (request()->isMethod('POST')) {
            $credentials = [
                'email' => request()->get('email'),
                'password' => request()->get('password')
            ];

            $remember = ( request()->exists('remember_me') ) ? true : false;

            if (auth()->guard('admin')->attempt($credentials, $remember)) {
                return redirect()->route('admin.home');
            }
        }

        return view('admin.auth.login');
    }


    public function logout()
    {
        if (auth()->guard('admin')->check()) {
            auth()->guard('admin')->logout();
            return redirect()->route('management.login');
        }
    }
}
