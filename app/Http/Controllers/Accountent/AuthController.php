<?php

namespace App\Http\Controllers\Accountent;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

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
            if (auth()->guard('accountent')->attempt($credentials, $remember)) {
                return redirect()->route('accountent.home');
            }
        }

        return view('accountent.auth.login');
    }


    public function logout()
    {
        if (auth()->guard('accountent')->check()) {
            auth()->guard('accountent')->logout();
            return redirect()->route('accountent.login');
        }
    }

    public function newAccount($email = null, $route = null)
    {
        $instance = \App\Instance::find(session('instanceId'));
        $accountent = new \App\Accountent;
        $accountent->email =$email;

        $tokenPassword = uniqid(uniqid(), true);
        $passwordTimeout = date('Y-m-d H:i:s', strtotime('+'.config('auth.timeout_password').' hours'));

        $accountent->password_token = $tokenPassword;
        $accountent->password_timeout = $passwordTimeout;
        $accountent->save();
        $instance->accountents()->attach($accountent);
        $data = array(
            "email" => $accountent->email,
            "url" => url()->route('accountent.remind-password', ['token'=>$accountent->password_token])
        );
        Mail::to($accountent->email)->send(new \App\Mail\CreateAccountent($data));
        return redirect()->route($route);
    }

    public function remindPassword($token = null)
    {
        $View = view('accountent.auth.remind-password');

        $accountent = \App\Accountent::where('password_token', '=', $token)->first();
        if ($accountent) {
            $timeOut = new \DateTime($accountent->password_timeout);
            $timeNow = new \DateTime();
            $lapsTime = ($timeOut->getTimestamp() - $timeNow->getTimestamp());

            // If is TimeOut
            if ($lapsTime <= 0) {
                $accountent->password_timeout = null;
                $accountent->password_token = null;
                $accountent->save();

                return $View->with('timeOver', true);
            } else {
                $data['timeOver'] = false;
                $data['tokenPassword'] = $token;

                if (request()->isMethod('post')) {
                    $rules = array(
                        'password' => 'required|min:5',
                        'password_confirmation' => 'required|same:password'
                    );

                    $validation = validator(request()->all(), $rules);

                    if ($validation->fails()) {
                        return redirect()->route('accountent.remind-password', ['token' => $token])
                                        ->withErrors($validation)
                                        ->withInput();
                    } else {
                        $accountent->password = bcrypt(request()->get('password'));
                        $accountent->password_timeout = null;
                        $accountent->password_token = null;
                        $accountent->save();

                        session()->flash('growl', 'success|'.\Lang::get('auth.msgSuccessChangePassword'));

                        return redirect()->route('accountent.login');
                    }
                }

                return $View->with($data);
            }
        } else {
            return response(view('errors.404'), 404);

            return $View;
        }
    }
}
