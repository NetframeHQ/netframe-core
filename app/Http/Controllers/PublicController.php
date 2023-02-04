<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use App\Instance;

class PublicController extends Controller
{

    public function __construct()
    {
        $apiKeys = config('external-api');
        view::share('googleMapsKey', $apiKeys['googleApi']['key']);
    }

    public function externalVisio($instanceId, $channelId, $slug)
    {
        $instance = Instance::find($instanceId);
        if ($instance != null) {
            $channel = $instance->channels()->whereId($channelId)->first();
            if ($channel != null) {
                // get access
                $access = $channel->externalAccess()
                    ->where('slug', '=', $slug)
                    ->where('start_at', '<=', date('Y-m-d H:i'))
                    ->where('expire_at', '>=', date('Y-m-d H:i'))
                    ->first();
                if ($access != null) {
                    if (request()->isMethod('POST')) {
                        // temporary redirect to jisti meet global service
                        $visioConf = config('external-api.jitsi');
                        $roomFullName = 'netframe-instance-' .
                            session('instanceId') .
                            '-channel-' .
                            $channel->id .
                            '-' .
                            $visioConf['keyMeet'];
                        return redirect()->to('https://meet.jit.si/' . $roomFullName);

                        $visioConf = config('external-api.jitsi');
                        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

                        // expire token
                        $now = strtotime(date('Y-m-d H:i:s'));
                        $expireToken = $now + 86400;

                        //$group = 'instance1';
                        $room = 'instance-'.$instanceId.'-channel-'.$channelId;
                        $iss = $visioConf['iss'];
                        $sub = $visioConf['url'];

                        $avatar = 'https://work.netframe.co/assets/img/avatar.jpg';

                        // Create token payload as a JSON string
                        $payload = json_encode([
                            "context"=> [
                                "user" => [
                                    "avatar" => $avatar,
                                    "name" => request()->get('firstname').' '.request()->get('lastname'),
                                    "email" => request()->get('email'),
                                    "id" => 'invite-'.rand(),
                                ],
                                //"group" => $group
                            ],
                            "aud" => "jitsi",
                            //"aud" => "co_netframe_visio",
                            "iss" => $iss,
                            "sub" => $sub,
                            //"sub" => "meet.jitsi",
                            "room" => $room,
                            "exp" => $expireToken
                        ]);

                        // Encode Header to Base64Url String
                        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
                        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
                        $signature = hash_hmac(
                            'sha256',
                            $base64UrlHeader . "." . $base64UrlPayload,
                            $visioConf['key'],
                            true
                        );
                        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
                        // Create JWT
                        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
                        return redirect()->to('https://'.$sub.'/'.$room.'?jwt='.$jwt);
                    }

                    $instanceLogo = $instance->getParameter('main_logo_2018', true);
                    if ($instanceLogo != null && $instanceLogo->parameter_value != null) {
                        $logoParams = json_decode($instanceLogo->parameter_value, true);
                        $mainLogoUrl = url()->route('instance.download', [
                            'imageType' => 'main_logo_2018',
                            'filename' => $logoParams['filename']
                        ]);
                        view()->share('instanceLogo', $mainLogoUrl);
                    }

                    $data = [
                        'access' => $access,
                    ];
                    return view('visio.external-login', $data);
                }
            }
        }
        return view('visio.external-login', ['error' => true]);
    }
}
