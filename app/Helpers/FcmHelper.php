<?php
namespace App\Helpers;

use App\UserMobileDevice;
use App\DeviceFcmToken;
use App\User;
use App\Repository\NotificationsRepository;

class FcmHelper
{
    public static function registerDuuid($duuid, $deviceType = 'mobile')
    {
        $user = auth()->guard('web')->user();
        $device = $user->devices()
            ->where('duuid', '=', $duuid)
            ->first();
        if ($device == null) {
            $device = new UserMobileDevice();
            $device->users_id = $user->id;
            $device->instances_id = session('instanceId');
            $device->duuid = $duuid;
            $device->save();
        }

        // include device in notifications
        for ($i = 1; $i <= 7; $i ++) {
            $userNotification = $user->userNotifications()->firstOrCreate([
                'instances_id' => session('instanceId'),
                'device' => $deviceType,
                // 'notification_identifier' => $fcmToken,
                'frequency' => $i
            ]);
            $userNotification->save();
        }
    }

    public static function deviceToken($duuid, $fcmToken)
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
                $deviceFcm->save();
            }
        }

        return response()->json([
            'result' => 'success'
        ]);
    }

    public static function buildFromNotif($usersIds, $notifId)
    {
        $notificationsRepository = new NotificationsRepository();
        $lastNotif = $notificationsRepository->findWaiting(null, false, $notifId);

        if (isset($lastNotif[0])) {
            $notif = $lastNotif[0];
            $notifTitle = strip_tags($notif->notifTitle);
            $notifTxt = strip_tags($notif->notifTxt);

            self::sendFcm($usersIds, $notifTitle, $notifTxt);
        }
    }

    public static function sendFcm($usersIds, $title, $message, $link = null)
    {
        $currentDay = date('w');
        $fcmKey = config('external-api.fmc_key');

        // get notifiables users
        $users = User::select('users.*')->whereIn('users.id', $usersIds)
            ->leftJoin('user_notifications', 'user_notifications.users_id', '=', 'users.id')
            ->whereIn('user_notifications.device', [
                'mobile',
                'browser'
            ])
            ->where('user_notifications.frequency', '=', $currentDay)
            ->groupBy('users.id')
            ->get();

        $tokenList = [];
        foreach ($users as $user) {
            // get user devices
            $userTokens = $user->tokensFcm()
                ->groupBy('fcm_token')
                ->pluck('fcm_token')
                ->toArray();
            $tokenList = array_merge($tokenList, $userTokens);
        }

        // dial with google api
        if (! empty($tokenList)) {
            $url = 'https://fcm.googleapis.com/fcm/send';

            $fields = [
                'registration_ids' => $tokenList,
                "priority" => "high",
                'notification' => [
                    'title' => $title,
                    'message' => 'Notif netframe ' . date('d/m/Y H:i:s'),
                    'vibrate' => 1,
                    'sound' => "default",
                    'color' => '#ffffff',
                    'icon' => 'fcm_push_icon',
                ]
                // 'badge' => 1
            ];

            if ($link != null) {
                $fields['notification'] ['click_action'] = $link;
            }

            if ($message != '' && $message != false) {
                $fields['notification']['body'] = $message;
            } else {
                $fields['notification']['body'] = '...';
            }

            $fields = json_encode($fields);

            $headers = array(
                'Authorization: key=' . $fcmKey,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            $result = curl_exec($ch);

            if ($result === false) {
                die('Curl failed: ' . curl_error($ch));
            }
            curl_close($ch);
        }
    }
}
