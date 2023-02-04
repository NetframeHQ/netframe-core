<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\DeviceFcmToken;
use App\UserNotification;
use App\Helpers\FcmHelper;

class AppController extends PublicController
{

    public function deviceToken($duuid, $fcmToken)
    {
        // test if device exists and match token
        $testDevice = DeviceFcmToken::where('device_uuid', '=', $duuid)->first();
        if ($testDevice == null) {
            $deviceFcm = new DeviceFcmToken();
            $deviceFcm->device_uuid = $duuid;
            $deviceFcm->fcm_token = $fcmToken;
            $deviceFcm->save();
        } else {
            if ($testDevice->fcm_token != $fcmToken) {
                // token has been change for this device, update db
                $testDevice->fcm_token = $fcmToken;
                $testDevice->save();
            }
        }

        // check if request is from browser
        if (request()->has('deviceType') && request()->get('deviceType') == 'browser') {
            // check if user has browser in his notificications parameters
            $user = auth()->user();
            if ($user != null) {
                // implement browser notifications
                // include device in notifications
                FcmHelper::registerDuuid($duuid, 'browser');
            }
        }

        return response()->json(['result' => 'success']);
    }
}
