<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Str;
use App\User;
use App\Country;
use App\Language;
use App\Instance;
use App\UserNotification;
use Illuminate\Support\Facades\Hash;

class AccountController extends BaseController
{

    public function __construct()
    {
        $this->middleware('checkAuth');
        parent::__construct();
    }

    /**
     * build account page
     */
    public function account()
    {
        if (!$this->Acl->getRights('user', auth()->guard('web')->user()->id)) {
            return redirect()->route('home');
        }
        $user = User::find(auth()->guard('web')->user()->id);
        $instance = Instance::find(session('instanceId'));

        $data = [];
        $data['user'] = $user;

        // user settings
        $data['listCountries'] = Country::listFromLocale();
        $data['listLanguageNetframe'] = Language::listLangNetframe();

        // custom fields attributes
        $data['customFields'] = $customFields = json_decode($instance->getParameter('custom_user_fields'), true) ?: [];
        $customFieldsInitialValues = [];
        foreach ($customFields as $slug => $value) {
            $customFieldsInitialValues[$slug] = $user->getParameter('custom_user_field_' . $slug);
        }

        $errors = false;
        if (request()->isMethod('POST')) {
            $configValidate = config('validation.user/setting');

            // Check if input email exist
            if (!is_null(request()->get('email'))) {
                $inputEmail = trim(request()->get('email'));
                // If email is different of email statement
                if ($inputEmail !== auth()->guard('web')->user()->email) {
                    $configValidate = $configValidate + array('email' => 'required|email|unique:users');
                } else {
                    $configValidate = $configValidate + array(
                        'email' => 'required|email'
                    );
                }
                $user->email = request()->get('email');
            } else {
                $user->email = auth()->guard('web')->user()->email;
                $configValidate = $configValidate + array(
                    'email' => 'required|email'
                );
            }

            $user->name = request()->get('name');
            $user->firstname = request()->get('firstname');
            $user->date_birth = request()->get('date_birth');
            $user->lang = request()->get('lang');

            app()->setLocale(request()->get('lang'));
            $user->pays = request()->get('pays');
            $user->gender = request()->get('gender');
            $user->codepostal = request()->get('codepostal');
            $user->city = request()->get('city');
            $user->nationality = request()->get('nationality');

            $user->function = request()->get('function');
            $user->phone = request()->get('phone');
            $user->desk_informations = request()->get('desk_informations');

            // Geolocation
            if (request()->get('latitude') != null && request()->get('longitude')) {
                $user->latitude = request()->get('latitude');
                $user->longitude = request()->get('longitude');
                $user->location = \App\Helpers\LocationHelper::getLocation($user->latitude, $user->longitude);
            }

            $validator = validator(request()->all(), $configValidate);
            if ($validator->fails()) {
                $errors = true;
            } else {
                $user->save();

                // manage custom fields
                if (request()->has('custom_field')) {
                    $customsFieldsValues = request()->get('custom_field');
                    foreach ($customFields as $slug => $value) {
                        if (isset($customsFieldsValues[$slug])) {
                            $user->setParameter('custom_user_field_' . $slug, $customsFieldsValues[$slug]);
                            $customFieldsInitialValues[$slug] = $customsFieldsValues[$slug];
                        } else {
                            $user->deleteParameter('custom_user_field_' . $slug);
                            unset($customFieldsInitialValues[$slug]);
                        }
                    }
                }

                auth()->guard('web')->user()->lang = request()->get('lang');
            }
            event(new \App\Events\UserUpdatedEvent($user));
        }

        // cutsom fields values
        $data['customFieldsValues'] = $customFieldsInitialValues;

        if ($errors) {
            return view('account.account', $data)->withErrors($validator);
        } else {
            return view('account.account', $data);
        }
    }

    public function editPassword()
    {
        $user = User::find(auth()->guard('web')->user()->id);

        if (request()->isMethod('POST')) {
            $rules = [
                "old_password" => "required",
                "password" => "required|min:6|confirmed"
            ];
            $validator = validator(request()->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
            } elseif (!Hash::check(request()->get('old_password'), $user->password)) {
                return response()->json(['errors' => ['old_password'=>['Ancien mot de passe invalide.']]]);
            } else {
                $user->password = bcrypt(request()->get('password'));
                $user->save();
                // relog user
                auth('web')->login($user);
                event(new \App\Events\UserUpdatedEvent($user));
                return response()->json([
                    'waitCloseModal' => 3000,
                    'view' => view('account.success-edit-password')->render()
                ]);
            }
        }
        return view('account.edit-password');
    }

    public function notifications()
    {
        if (!$this->Acl->getRights('user', auth()->guard('web')->user()->id)) {
            return redirect()->route('home');
        }

        $notifiableDevices = config('users.notificationsDevices');
        $user = User::find(auth()->guard('web')->user()->id);
        $data = [];
        $data['user'] = $user;

        if (request()->isMethod('POST')) {
            //
            $device = request()->get('device');
            if ($notifiableDevices[$device] == 'days') {
                for ($j=0; $j<=6; $j++) {
                    $userDayNotif = $user
                        ->userNotifications()
                        ->where('device', '=', $device)
                        ->where('frequency', '=', $j)
                        ->first();
                    $currentDay = request()->get('day_' . $j);
                    if ($currentDay == 1 && $userDayNotif == null) {
                        // insert notification
                        $userNotif = new UserNotification();
                        $userNotif->users_id = auth()->guard('web')->user()->id;
                        $userNotif->instances_id = session('instanceId');
                        $userNotif->device = $device;
                        $userNotif->frequency = $j;
                        $userNotif->save();
                    } elseif ($currentDay == null && $userDayNotif != null) {
                        // delete notification
                        $userDayNotif->delete();
                    }
                }
            }
        }

        // get distinct notifications tye of user
        $userNotifications = [];
        foreach ($notifiableDevices as $device => $frequency) {
            if ($frequency == 'days') {
                for ($j=0; $j<=6; $j++) {
                    $userNotifications[$device][$j] = false;
                }
                $deviceNotifications = $user
                    ->userNotifications()
                    ->where('device', '=', $device)
                    ->orderBy('frequency')
                    ->get();
                foreach ($deviceNotifications as $deviceNotification) {
                    $userNotifications[$device][$deviceNotification->frequency] = true;
                }
            } else {
                $deviceNotifications = $user->userNotifications()->where('device', '=', $device)->get();
                foreach ($deviceNotifications as $deviceNotification) {
                    $userNotifications[$device]['notification_identifier'] = true;
                }
            }
        }
        $data['notifiableDevices'] = $notifiableDevices;
        $data['userNotifications'] = $userNotifications;

        return view('account.notifications', $data);
    }

    public function privacy()
    {
        $data = [];

        $user = User::find(auth()->guard('web')->user()->id);
        $data['user'] = $user;

        if (request()->isMethod('POST')) {
            $user = User::find(auth()->guard('web')->user()->id);
            $user->gdpr_agrement = (request()->has('gdpr')) ? 1 : 0;
            $user->modal_gdpr = 0;
            $user->save();
            $data['gdpr'] = (request()->has('gdpr')) ? 1 : 0;

            if (request()->has('fromModal')) {
                $dataJson = ['agrementSent' => true];
                $gdpr_modal = view('account.gdpr-modal', $dataJson)->render();
                return response()->json(['view' => $gdpr_modal]);
            }
        } else {
            $data['gdpr'] = auth()->guard('web')->user()->gdpr_agrement;
        }
        return view('account.privacy', $data);
    }

    public function more()
    {
        $instance = Instance::find(session('instanceId'));
        $data = [];
        $values = [];
        $user = auth()->guard('web')->user();
        $data['user'] = $user;
        $data['fields'] = $fields = json_decode($instance->getParameter('custom_user_fields'), true) ?: [];

        foreach ($fields as $slug => $value) {
            $values[$slug] = $user->getParameter('custom_user_field_' . $slug);
        }
        $data['values'] = $values;//json_decode($user->getParameter('custom_user_fields'), true);
        //Gestion des checkbox avant de repasser sur les apis
        if (request()->isMethod('POST')) {
            $data = request()->except('_token');
            // dd($data);
            foreach ($data as $key => $value) {
                if (!isset($value) && in_array($key, $fields)  && $fields[$key] == 'check') {
                    $data[$key] = true;
                }
            }
            foreach ($fields as $slug => $value) {
                if (isset($data[$slug])) {
                    $user->setParameter('custom_user_field_' . $slug, $data[$slug]);
                }
            }
            // $user->setParameter('custom_user_fields', json_encode($data));
            session()->flash('successRecord', true);
            return redirect()->route('account.more');
        }
        return view('account.more', $data);
    }

    public function calendars()
    {
        $user = auth()->guard('web')->user();
        if (request()->isMethod('POST')) {
            $email = request()->get('postData');
            $user->deleteCalendar($email);
            return response()->json([
                'email' => $email
            ]);
        }
        $data = [];
        $data['user'] = $user;
        $data['calendars'] = $user->calendars();
        return view('account.calendars', $data);
    }
}
