<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\BoardingDemand;
use App\Boarding;
use App\Instance;
use App\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class BoardingController extends PublicController
{

    public function __construct()
    {
        $this->middleware('noInstance', ['except' => ['byKey', 'attachInstance', 'confirmAccountCreated']]);
        parent::__construct();
    }

    public function home()
    {
        $data = [
            'stepBoarding' => 1,
        ];
        return view('boarding.home', $data);
    }

    public function sendCode()
    {
        // validator
        $validator = validator(request()->all(), config('validation.boarding'));
        if ($validator->fails()) {
            $data = [
                'stepBoarding' => 1,
            ];
            // check if email exists
            $checkEmail = Boarding::where('email', '=', request()->get('email'))->first();
            if ($checkEmail != null) {
                $data['errorCode'] = 'emailExists';
                session(['boarding.boarding' => $checkEmail->id]);
            }
            return view('boarding.home', $data)->withErrors($validator);
        } else {
            //check if boarding email is in progress
            $checkEmail = Boarding::where('email', '=', request()->get('email'))->first();
            $checkUser = User::where('email', '=', request()->get('email'))->first();
            if ($checkEmail != null) {
                // boarding exists, proposal resend mail
                $data = [
                    'stepBoarding' => 1,
                    'errorCode' => 'emailExists',
                ];
                session(['boarding.boarding' => $checkEmail->id]);
                return view('boarding.home', $data);
            } elseif ($checkUser != null) {
                // user exists
                $data = [
                    'stepBoarding' => 1,
                    'errorCode' => 'userExists',
                ];
                return view('boarding.home', $data);
            } else {
                // proccess to mail send with code
                $boardingKey = Boarding::generateFirstKey();

                if (config('netframe.log_mails_data')) {
                    Log::debug('Sending ' . request()->get('email') . ' inscription code ' . $boardingKey);
                }

                if (!session()->has('userLang') || empty(session('userLang'))) {
                    session(['userLang' => 'en']);
                }

                $boarding = new Boarding();
                $boarding->email = request()->get('email');
                $boarding->boarding_key = $boardingKey;
                $boarding->instances_id = null;
                $boarding->lang = session('userLang');
                $boarding->save();

                Mail::to($boarding->email)->send(new BoardingDemand($boarding));

                session(['boarding.boarding' => $boarding->id]);

                return redirect()->route('boarding.checkcode');
            }
        }

        //check if email exists in database

            //if exists proposal of connect existing instance or create new
    }

    public function resentLink()
    {
        if (!session()->has('boargingReset') && !session()->has('boargingResetEmail')) {
            // redirect to login
            return redirect()->route('login');
        } else {
            $boarding = Boarding::where('id', '=', session('boargingReset'))
                ->where('email', '=', session('boargingResetEmail'))
                ->first();

            session()->forget(['boargingReset', 'boargingResetEmail']);
            if ($boarding == null) {
                // redirect to login
                return redirect()->route('login');
            } else {
                if ($boarding->slug != null) {
                    // resent boarding link and redirect to success page
                    $boarding->emailKey = base64_encode(
                        $boarding->created_at . '|' . $boarding->id . '|' . $boarding->email
                    );
                    $boarding->userFrom = $boarding->instance->admins->first();
                    $boarding->boardingUrl = $boarding->instance->getUrl()
                        . '/boarding/key/' . $boarding->slug
                        . '/' . $boarding->emailKey;

                    //send mail
                    Mail::to($boarding->email)->send(new BoardingDemand($boarding));

                    // redirect to confirm page
                    return view('boarding.resent');
                } else {
                    Mail::to($boarding->email)->send(new BoardingDemand($boarding));

                    // redirect to input code
                    session(['boarding.boarding' => $boarding->id]);

                    return redirect()->route('boarding.checkcode');
                }
            }
        }
    }

    public function newCode()
    {
        if (!session()->has('boarding.boarding')) {
        } else {
            $boarding = Boarding::findOrFail(session('boarding.boarding'));
            $boardingKey = Boarding::generateFirstKey();
            $boarding->boarding_key = $boardingKey;
            $boarding->save();

            Mail::to($boarding->email)->send(new BoardingDemand($boarding));

            $data = [
                'errorCode' => 'newCode',
                'boarding' => $boarding,
                'stepBoarding' => 1,
            ];
            return view('boarding.check-code', $data);
        }
    }

    /*
     * string $type = user, mainUser
     */
    public function confirmAccountCreated($type)
    {
        if ($type == 'mainUser') {
            $routeNext = url()->route('boarding.create.instance');
        } elseif ($type == 'user') {
            $routeNext = url()->route('user.timeline');
        }

        $data = [
            'nextRoute' => $routeNext,
            'stepBoarding' => 2,
            'nbSteps' => (session()->has('boarding.main-user')) ? 4 : 2,
        ];

        return view('boarding.confirm-create', $data);
    }

    public function byKey($boardingKey, $encodedEmail = null)
    {
        $boarding = null;
        $instance = null;

        if ($encodedEmail != null) {
            $emailDecode = base64_decode($encodedEmail);
            if ($emailDecode != null) {
                $emailTab = explode('|', $emailDecode);
                $email = $emailTab[2];
                $boardingId = $emailTab[1];
                $boardingCreated = $emailTab[0];
                $boarding = Boarding::where('slug', '=', $boardingKey)
                    ->where('email', '=', $email)
                    ->where('instances_id', '=', session('instanceId'))
                    ->where('id', '=', $boardingId)
                    ->where('created_at', '=', $boardingCreated)
                    ->first();

                if ($boarding != null) {
                    session([
                        'boarding.user-email' => $boarding->email,
                        'boarding.waitingInstanceId' => $boarding->instances_id,
                        'boarding.boarding' => $boarding->id,
                        'boarding.byBoardingKeyEmail' => $boarding->boarding_key,
                    ]);
                    $instance = Instance::find($boarding->instances_id);

                    // check if user already have an account
                    $checkUser = User::where('email', '=', $email)->first();
                    if ($checkUser != null) {
                        $existingUser = true;
                    }
                }
            }
        } else {
            $instance = Instance::where('id', '=', session('instanceId'))
                ->whereHas('parameters', function ($p) use ($boardingKey) {
                    $p->where('parameter_name', '=', 'boarding_invite_key')
                      ->where('parameter_value', '=', $boardingKey);
                })->first();
            if ($instance != null) {
                //check if boarding by key enable
                $disableKey = $instance->getParameter('boarding_on_key_disable');
                if ($disableKey != null && $disableKey == 0) {
                    session([
                        'boarding.waitingInstanceId' => $instance->id,
                        'boarding.byBoardingKey' => $boardingKey,
                    ]);
                } else {
                    $instance = null;
                }
            }
        }

        if ($instance != null || $boarding != null) {
            //check instance limits
            $billingOffer = $instance->getParameter('billing_offer');
            $maxUsers = config('billing.offer.'.$billingOffer.'.maxUsers');
            if ($maxUsers > 0 && $instance->users->count() >= $maxUsers) {
                $data = [];
                $data['errorCode'] = 'maxUserReach';
                return view('boarding.error-key', $data);
            } elseif (isset($existingUser) && $existingUser) {
                // redirect to auth page with user password check
                return redirect()->route('boarding.attach.instance');
            } else {
                return redirect()->route('auth.register');
            }
        } else {
            $data = [];
            $data['errorCode'] = 'codeUrlError';
            return view('boarding.error-key', $data);
        }
    }

    /*
     * log existing user to a new instance
     */
    public function attachInstance()
    {
        if (request()->isMethod('post')) {
            $credentials = [
                "email" => session('boarding.user-email'),
                "password" => request()->get('password'),
                "active" => 1
            ];
            if (auth()->guard('web')->check()) {
                $checkUser = auth()->guard('web')->validate($credentials);
            } else {
                $remember = (request()->has('remember_token')) ? true : false;
                $checkUser = auth()->guard('web')->attempt($credentials, $remember);
            }
            if (!$checkUser) {
                session()->flash('login_errors', trans('auth.msg_badpassword'));
            } else {
                $user = User::where('email', '=', $credentials['email'])->first();

                // attach user to instance with participant role
                $user->instances()->attach(session('boarding.waitingInstanceId'), ['roles_id' => 5]);

                if (!auth()->guard('web')->check()) {
                }


                //remove boarding informations
                if (session()->has('boarding')) {
                    if (session()->has('boarding.boarding')) {
                        $boarding->delete();
                    }
                    session()->forget('boarding');
                }
            }
        }

        return view('boarding.auth-existing-user');
    }

    public function checkCode()
    {
        if (session()->has('boarding.boarding')) {
            $boarding = Boarding::findOrFail(session('boarding.boarding'));
        } elseif (request()->has('slug') && request()->get('slug') != null) {
            $boarding = Boarding::where('slug', '=', request()->get('slug'))->first();
            session(['boarding.boarding' => $boarding->id]);
        } else {
            return redirect()->route('boarding.home');
        }

        if (request()->isMethod('POST')) {
            $validator = validator(request()->all(), config('validation.boarding/code'));
            if ($validator->fails()) {
                $data= [
                    'errorCode' => 'codeMatch',
                    'boarding' => $boarding,
                    'stepBoarding' => 1,
                ];
                return view('boarding.check-code', $data)->withErrors($validator);
            } else {
                $code = request()->get('n1') . request()->get('n2') . request()->get('n3')
                    . '-' . request()->get('n4') . request()->get('n5') . request()->get('n6');

                $checkBoarding = Boarding::where('boarding_key', '=', $code)
                    ->where('id', '=', session('boarding.boarding'))
                    ->first();

                if ($checkBoarding == null) {
                    $data = [
                        'errorCode' => 'codeMatch',
                        'boarding' => $boarding,
                        'stepBoarding' => 1,
                    ];
                    return view('boarding.check-code', $data);
                } elseif ($checkBoarding->id == $boarding->id) {
                    session([
                        'boarding.user-email' => $boarding->email,
                        'boarding.boarding' => $boarding->id,
                        'boarding.waitingInstanceId' => 'wait',
                        'boarding.main-user' => 1
                    ]);

                    //return redirect()->route('boarding.create.instance');
                    return redirect()->route('auth.register');
                } else {
                    return redirect()->route('boarding.home');
                }
            }
        } else {
            $data= [
                'boarding' => $boarding,
                'stepBoarding' => 1,
            ];
            return view('boarding.check-code', $data);
        }
    }

    public function createInstance()
    {
        if (request()->isMethod('POST')
            && (!session()->has('boarding.waitingInstanceId')
              || session('boarding.waitingInstanceId') == 'wait')
        ) {
            $validator = validator(request()->all(), config('validation.boarding/instance'));
            if ($validator->fails()) {
                return view('boarding.instance')->withErrors($validator);
            } else {
                $instanceName = request()->get('instance_name');
                $instance = new instance();
                $instance = $instance->createNew($instanceName);

                session([
                    'boarding.waitingInstanceId' => $instance->id,
                    'boarding.main-user' => 1
                ]);

                // redirect to next step boarding
                $user = User::find(session('boarding.main-user-id'));
                return $user->finalizeBoarding($user, $instance);

                //return redirect()->to($instance->getUrl().'/register');
            }
        } elseif (session()->has('boarding.waitingInstanceId')
            && session('boarding.waitingInstanceId') != 'wait'
            && session()->has('boarding.main-user-id')) {
            // redirect to next step boarding
            session(['inCreation' => true]);
            return redirect()->route('boarding.admin.step1');
        } else {
            $data = [
                'stepBoarding' => 3,
            ];
            return view('boarding.instance', $data);
        }
    }
}
