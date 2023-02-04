<?php
namespace App\Http\Controllers\Channel;

use App\Http\Controllers\BaseController;
use \App\Helpers\Lib\Acl;
use App\Channel;
use App\ChannelsExternalsAccess;

class VisioController extends BaseController
{

    public function manageLink($id)
    {
        $channel = Channel::find($id);

        if (!is_null($id) && (!Acl::getRights('channel', $id) || Acl::getRights('channel', $id) > 3 )) {
         //   return response(view('errors.403'), 403);
        }

        $timezone_identifiers = \DateTimeZone::listIdentifiers();

        foreach ($timezone_identifiers as $key => $tz) {
            if (strtolower($tz) == strtolower(session('userTimezone'))) {
                $userTimeZone = $key;
            }
        }
        if (!isset($userTimeZone)) {
            foreach ($timezone_identifiers as $key => $tz) {
                if (strtolower($tz) == strtolower('Europe/Paris')) {
                    $userTimeZone = $key;
                }
            }
        }

        $data = [
            'userTimeZone' => $userTimeZone,
            'timeZones' => $timezone_identifiers,
            'channel' => $channel,
        ];

        return view('visio.manage-links', $data);
    }

    public function addLink()
    {
        if (!request()->has('channelId')
            && (!Acl::getRights('channel', request()->get('channelId'))
                || Acl::getRights('channel', request()->get('channelId')) > 3 )
        ) {
            return response(view('errors.403'), 403);
        }

        $channel = Channel::find(request()->get('channelId'));

        $start = request()->get('startDate').' '.request()->get('startTime');
        $end = request()->get('endDate').' '.request()->get('endTime');

        if (request()->has('timezone') && !empty(request()->get('timezone'))) {
            $tzId = request()->get('timezone');
            $timezone_identifiers = \DateTimeZone::listIdentifiers();
            $tz = $timezone_identifiers[request()->get('timezone')];
        } else {
            $tzId = null;
            $tz = session('userTimezone');
        }

        $utcStartDate = \App\Helpers\DateHelper::convertToLocalUTC($start, $tzId);
        $utcEndDate = \App\Helpers\DateHelper::convertToLocalUTC($end, $tzId);

        $access = new ChannelsExternalsAccess();
        $access->channels_id = $channel->id;
        $access->slug = rand();
        $access->token = bcrypt(rand());
        $access->start_at = $utcStartDate["datetime"];
        $access->expire_at = $utcEndDate["datetime"];
        $access->timezone = $tz;
        $access->lastname = request()->get('lastname');
        $access->firstname = request()->get('firstname');
        $access->email = request()->get('email');
        $access->save();

        $view = view('visio.link-line', ['access' => $access])->render();

        return response()->json(['view' => $view]);
    }

    public function deletelink($channel_id, $access_id)
    {
        if (!Acl::getRights('channel', $channel_id) || Acl::getRights('channel', $channel_id) > 3) {
            return response(view('errors.403'), 403);
        }

        $access = ChannelsExternalsAccess::where('id', '=', $access_id)
        ->where('channels_id', '=', $channel_id)
            ->first();
        if ($access == null) {
            return response(view('errors.403'), 403);
        } else {
            $access->delete();
            $data = [
                'delete' => true,
                'targetId' => '#access-'.$access_id,
            ];
            return response()->json($data);
        }
    }
}
