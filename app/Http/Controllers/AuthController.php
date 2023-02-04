<?php

namespace App\Http\Controllers;

//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use App\User;
use App\Netframe;
use App\Helpers\SessionHelper;
use App\Instance;
use App\Boarding;
use App\UserNotification;
use App\MediasFolder;
use App\Events\UserLogguedEvent;
use App\VirtualUser;

class AuthController extends PublicController
{

    public function __construct()
    {
        parent::__construct();
        // $this->beforeFilters('csrf', array('on' => 'post'));
    }


    /**
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register($messageRegister = false)
    {

        // servira par la suite afin de rediriger l'utilisateur sur la page où il était
        $HTTPreferer = request()->header('referer');

        $validationsRules = config('validation.auth/register');
        if (session()->has('boarding.main-user')) {
            $validationsRules = array_merge($validationsRules, ["cgv" => "required|in:1"]);
        }

        $validation = validator(request()->all(), $validationsRules);

        if (request()->isMethod('post')) {
            if (!session()->has('boarding.waitingInstanceId')
                || session('boarding.waitingInstanceId') != request()->get('instanceId')) {
                return redirect()->route('boarding.home');
            }
            // If Validation register Failed
            if ($validation->fails()) {
                return redirect()->route('auth.register')->withErrors($validation)->withInput();
            }

            //check instance limit
            if (!session()->has('boarding.main-user')) {
                $instance = Instance::find(session('boarding.waitingInstanceId'));
                $billingOffer = $instance->getParameter('billing_offer');
                $maxUsers = config('billing.offer.'.$billingOffer.'.maxUsers');
                if ($maxUsers > 0 && $instance->users->count() >= $maxUsers) {
                    $data = [
                        'errorCode' => 'maxUserReach',
                        'stepBoarding' => 1,
                    ];
                    return view('boarding.error-key', $data);
                }
            }

            // Validation is Success
            $user = new User();

            $user->ip = SessionHelper::getIp();
            $user->lang = \Lang::getLocale();
            $user->firstname = request()->get(trim('firstname'));
            $user->name = request()->get(trim('name'));
            $user->email = request()->get('email');
            $user->password = bcrypt(request()->get(trim('password')));

            $user->date_birth = request()->get('date_birth');
            $user->gender = request()->get(trim('gender'));
            $user->gdpr_agrement = (request()->has('gdpr')) ? 1 : 0;
            $user->modal_gdpr = 0;
            $user->confidentiality = 1;

            // Generate Uniq Key for slug profile
            $uniqKey = uniqid();
            // Check if uniq id in slug exist
            if (User::where('slug', '=', $uniqKey)->exists()) {
                $uniqKey = uniqid();
            }
            $user->slug = $uniqKey;
            $user->save();

            if (session()->has('boarding.main-user')) {
                // redirect to create instance
                session(['boarding.main-user-id' => $user->id]);

                return redirect()->route('boarding.confirm.creation', ['type' => 'mainUser']);
            }

            return $user->finalizeBoarding($user, $instance);
        }

        //check session waiting instance or boarding key
        $data = [];
        $data['messageRegister'] = $messageRegister;

        if (!session()->has('boarding.waitingInstanceId')
            && (!session()->has('boarding.byBoardingKeyEmail') || !session()->has('boarding.byBoardingKey'))) {
            return redirect()->route('boarding.home');
        } elseif (session('boarding.waitingInstanceId') != 'wait') {
            if (session()->has('boarding.byBoardingKeyEmail')) {
                $boarding = Boarding::where('boarding_key', '=', session('boarding.byBoardingKeyEmail'))->first();
                if ($boarding != null) {
                    session([
                        'boarding.waitingInstanceId' => $boarding->instances_id,
                        'boarding.user-email' => $boarding->email,
                        'boarding.boarding' => $boarding->id,
                    ]);
                } else {
                    return redirect()->route('boarding.home');
                }
            }

            if (session('boarding.waitingInstanceId') == 'wait' && !session()->has('boarding.main-user')) {
                return redirect()->route('boarding.home');
            } else {
                //verify instance existence
                $instance = Instance::find(session('boarding.waitingInstanceId'));
                if ($instance != null) {
                    $data['instanceId'] = $instance->id;
                    $data['email'] = session('boarding.user-email');
                } else {
                    return redirect()->route('boarding.home');
                }
            }
        } else {
            $data['instanceId'] = session('boarding.waitingInstanceId');
            $data['email'] = session('boarding.user-email');
        }
        $data['stepBoarding'] = (session()->has('boarding.main-user')) ? 2 : 1;
        $data['nbSteps'] = (session()->has('boarding.main-user')) ? 4 : 2;

        return view('auth.form-register', $data);
    }

    /**
     * Display a listing of the resource.
     * GET /auth
     *
     * @return Response
     */
    public function login($messageLogin = '')
    {
        $email ='';

        session()->forget(['boargingReset', 'boargingResetEmail']);

        // \Log::error(json_encode(request()->all()));

        // servira par la suite afin de rediriger l'utilisateur sur la page où il était
        $HTTPreferer = request()->header('referer');

        if (request()->isMethod('post')) {
            $validation = validator(request()->all(), config('validation.auth/login'));
            // ECHEC LOGIN
            if ($validation->fails()) {
                session()->flash('login_errors', trans('auth.msg_login_required'));
            } else {
                $credentials = [
                    "email" => request()->get("email"),
                    "password" => request()->get('password'),
                    "active" => 1
                ];
                $virtualUser = null;
                $user = User::where('email', '=', $credentials['email'])->first();
                $remember = (request()->has('remember_token')) ? true : false;

                // check if instance is set (subdomain) to test if virtual user app is active
                $virtualUserAuth = false;
                if (session()->has('instanceId')) {
                    // check app, test auth
                    $instance = Instance::find(session('instanceId'));
                    if ($instance != null && $instance->appActive('virtualUsers')) {
                        $virtualUserAuth = false;
                        $virtualUser = VirtualUser::where('email', '=', $credentials['email'])->first();
                        if ($virtualUser != null) {
                            $user = $virtualUser->user;
                            if ($user->active == 1) {
                                $virtualUserAuth = auth()->guard('virtualusers')->attempt($credentials, $remember);
                            }
                        }
                    }
                }

                if (auth()->guard('web')->attempt($credentials, $remember) ||
                    $virtualUserAuth
                    ) {
                    if (auth()->guard('virtualusers')->check()) {
                        // unlog virtual user and log on corresponding user
                        $virtualUser = VirtualUser::whereId(auth()->guard('virtualusers')->user()->id)->first();
                        if ($virtualUser != null) {
                            $correspondingUser = $virtualUser->user;
                            auth()->guard('virtualusers')->logout();
                            auth('web')->login($correspondingUser);
                        }
                    }
                    $user = auth()->guard('web')->user();
                    if (session()->has('instanceId')) {
                        $profile = User::whereId(auth()->guard('web')->user()->id)
                            ->whereHas('instances', function ($wI) {
                                if (session()->has('instanceId')) {
                                    $wI->where('id', '=', session('instanceId'));
                                }
                            })
                            ->first();
                        if ($profile == null) {
                            /*
                            SessionHelper::destroyProfile();
                            auth()->guard('web')->logout();
                            return redirect(config('netframe.baseLoginUrl'));
                            */
                            $profile = User::find(auth()->guard('web')->user()->id);
                            //$instance = $profile->instances()->where('id', '=', session('instanceId'))->first();
                            $instance = $profile->instances()->first();
                            session(['instanceId' => $instance->id]);
                        }
                    } else {
                        $profile = User::find(auth()->guard('web')->user()->id);
                        $instance = $profile->instances->first();
                        session(['instanceId' => $instance->id]);
                    }

                    // record device id
                    if (request()->has('duuid')) {
                        \App\Helpers\FcmHelper::registerDuuid(request()->get('duuid'));
                    }

                    \Lang::setLocale(auth()->guard('web')->user()->lang);

                    // Initialize and Storage in profiles session
                    $user->storeProfileSession($profile, "user");

                    $instance = $profile->instances->first();

                    return redirect()->route('netframe.workspace.home');

                    //return redirect()->route('user.timeline');
                } else {
                    if ($virtualUser == null && $user != null && $user->active == 0) {
                        session()->flash('login_errors', trans('auth.msg_inactiveAccount'));
                    } elseif ($virtualUser == null && $user != null && $user->active == 1) {
                        session()->flash('login_errors', trans('auth.msg_badpassword'));
                    } elseif ($virtualUser != null && ($user->active == 0 || $virtualUser->active == 0)) {
                        session()->flash('login_errors', trans('auth.msg_inactiveAccount'));
                    } elseif ($virtualUser != null && $user->active == 1) {
                        session()->flash('login_errors', trans('auth.msg_badpassword'));
                    } else {
                        // test if this email has boarding waiting
                        $email = request()->get('email');
                        $testBoarding = Boarding::where('email', '=', trim($email))->first();
                        if ($testBoarding != null) {
                            session()->flash('login_errors', trans('auth.msg_boardingWaiting'));
                            session(['boargingReset' => $testBoarding->id]);
                            session(['boargingResetEmail' => $email]);
                            session()->flash('resend_boarding_link', true);
                        } else {
                            session()->flash('login_errors', trans('auth.msg_badlogin'));
                        }
                    }
                }
            }
        }

        $data = [
            'hideBottomMobile' => true,
            'messageLogin' => $messageLogin,
            'email' => $email
        ];

        // check if request is from mobile device
        if (request()->has('duuid')) {
            $data['duuid'] = request()->get('duuid');
        }

        return view('auth.login', $data);
    }

    public function autoLogin()
    {
        $user = auth()->guard('web')->user();

        session()->regenerate();
        // Initialize and Storage in profiles session
        $return = $user->storeProfileSession($user, "user");

        return $return;
    }


    /**
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        SessionHelper::destroyProfile();
        auth()->guard('web')->logout();
        session()->flush();
        return redirect()->to('/');
    }

    public function forgotPassword()
    {
        $data = array();
        $View = view('auth.forgot-password');

        if (request()->isMethod('post')) {
            $inputDatas = array(
                'email' => trim(request()->get('email'))
            );

            $rule = ['email' => 'required|email|exists:users'];
            $validation = validator($inputDatas, $rule);

            // check if instance is set (subdomain) to test if vurtual user app is active
            $virtualUserAuth = false;
            if (session()->has('instanceId')) {
                // check app, test auth
                $instance = Instance::find(session('instanceId'));
                if ($instance != null && $instance->appActive('virtualUsers')) {
                    $ruleVirtualUser = ['email' => 'required|email|exists:virtual_users'];
                    $validationVirtualUser = validator($inputDatas, $ruleVirtualUser);
                }
            }

            if ($validation->fails() &&
                (!isset($validationVirtualUser) || (isset($validationVirtualUser) && $validationVirtualUser->fails()))
                ) {
                return redirect()->route('auth.forgotPassword')
                    ->withErrors($validation)
                    ->withInput();
            } else {
                $tokenPassword = uniqid(uniqid(), true);
                $passwordTimeout = date('Y-m-d H:i:s', strtotime('+'.config('auth.timeout_password').' hours'));

                if (!$validation->fails()) {
                    $user = User::where('email', '=', $inputDatas['email'])->first();
                    $user->password_token = $tokenPassword;
                    $user->password_timeout = $passwordTimeout;
                    $user->save();
                } elseif (isset($validationVirtualUser) && !$validationVirtualUser->fails()) {
                    $user = VirtualUser::where('email', '=', $inputDatas['email'])->first();
                    $user->password_token = $tokenPassword;
                    $user->password_timeout = $passwordTimeout;
                    $user->save();
                }

                $dataEmail = array(
                    'uriToken' => url()->action('AuthController@remindPassword', $user->password_token),
                    'tokenPassword' => $user->password_token,
                    'user' => $user,
                );

                Mail::to($user->email)->send(new ForgotPassword($dataEmail));
                /*
                \Mail::send('emails.auth.reminder', $dataMail, function($m) use ($user)
                {
                    //$m->to($user->email, 'support')->subject(trans('auth.mailSendPassword'));
                    $m->to($user->email, 'support')->subject(trans('email.resetPassword.subject'));
                });
                */

                return $View->with('success', true)
                            ->with('emailSending', $user->email);
            }
        }

        return $View;
    }



    public function remindPassword($token = null)
    {
        $View = view('auth.remind-password');

        $user = User::where('password_token', '=', $token)->first();

        if ($user == null) {
            $user = VirtualUser::where('password_token', '=', $token)->first();
        }

        if ($user) {
            $timeOut = new \DateTime($user->password_timeout);
            $timeNow = new \DateTime();
//             $interval = $timeOut->diff($timeNow)->format('%Y-%m-%d %H:%i:%s');
//             $lapsTime = ($interval - $timeNow->format('H:i:s'));
            $lapsTime = ($timeOut->getTimestamp() - $timeNow->getTimestamp());

            // If is TimeOut
            if ($lapsTime <= 0) {
                // Reset field remind password
                $user->password_timeout = null;
                $user->password_token = null;
                $user->save();

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
                        return redirect()->route('auth.remindPassword', ['token' => $token])
                                        ->withErrors($validation)
                                        ->withInput();
                    } else {
                        $user->password = bcrypt(request()->get('password'));
                        $user->password_timeout = null;
                        $user->password_token = null;
                        $user->save();

                        session()->flash('growl', 'success|'.\Lang::get('auth.msgSuccessChangePassword'));

                        return redirect()->route('login');
                    }
                }

                return $View->with($data);
            }
        } else {
            return response(view('errors.404'), 404);
        }
    }
}
