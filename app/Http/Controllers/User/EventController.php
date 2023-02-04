<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Events\PostAction;
use App\TEvent;
use App\Newsfeed;
use App\Events\NewAction;
use App\Events\PostNotif;
use App\Events\RemoveNotif;
use App\Events\RemoveAction;

class EventController extends BaseController
{
    public function __construct()
    {
        $this->middleware('checkAuth');
        parent::__construct();
    }

    /**
     * function to indicate user participate to event
     */
    public function participate($eventId)
    {
        $dataJson = array();
        $dataJson['event'] = $eventId;

        $event = TEvent::find($eventId);

        if ($event->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        if ($event != null) {
            //build notification array
            $notifArray = array(
                'instances_id'   => session('instanceId'),
                'author_id'      => $event->users_id,
                'author_type'    => 'App\\'.ucfirst('user'),
                'type'           => 'participateEvent',
                'user_from'      => auth()->guard('web')->user()->id,
                'parameter'      => json_encode(['event_id' => $event->id]),
            );


            //verify relation
            if ($event->participantsUsers()->where(
                'users_id',
                '=',
                auth()->guard('web')->user()->id
            )->first() == null) {
                $event->participantsUsers()->attach(auth()->guard('web')->user()->id, array('status' => '1'));
                $event->increment('participants', 1);

                $dataJson['participate'] = true;
                $dataJson['increment'] = 1;

                //update newsfeed date of event post
                event(new PostAction("TEvent", $eventId));

                //send notification to event owner
                event(new PostNotif($notifArray));

                $event->posts()->first()->touch();
            } else {
                $event->participantsUsers()->detach(auth()->guard('web')->user()->id);
                $event->decrement('participants', 1);

                $dataJson['participate'] = false;
                $dataJson['increment'] = -1;

                //remove notification to event owner
                event(new RemoveNotif($notifArray));

                //remove netframeAction for participant
                event(new RemoveAction(
                    'participant_event',
                    $eventId,
                    'TEvent',
                    auth()->guard('web')->user()->id,
                    'user'
                ));
            }

            return response()->json($dataJson);
        }
    }

    /**
     * return view with participant list
     * @param int $eventId event id
     */
    public function participants($eventId)
    {
        $event = TEvent::find($eventId);

        if ($event->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        $data = array();

        if ($event != null) {
            $data['participants'] = $event->participantsUsers()->get();
        } else {
            $data['participants'] = null;
        }

        return view('event.participants', $data)->render();
    }

    public function dashboard()
    {
        $events = NewsFeed::select()
            ->where('instances_id', '=', session('instanceId'))
            ->where('post_type', '=', ['TEvent'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $data = array();
        $data['apiKeyGoogle'] = config('external-api.googleApi.key');
        $data['searchDistance'] = 35000;
        $data['events'] = $events;
        $data['autoScroll'] = 0;

        return view('event.dashboard', $data);
    }

    public function search()
    {
        $keywords = trim(request()->get('keywords'));
        $distance = request()->get('distance');
        $lat = request()->get('latitude');
        $lng = request()->get('longitude');
        if ($lat == '' && $lng == '') {
            $lat = session("lat");
            $lng = session("lng");
            $distance = 35000;
        }

        if ($keywords != '') {
            $against = str_replace(' ', '*', '*'.$keywords.'*');
            $compareRank = '>';
        } else {
            $against = '';
            $compareRank = '>=';
        }

        $events = NewsFeed::select()
            ->leftJoin('events as evt', 'evt.id', '=', 'news_feeds.post_id')
            ->addSelect(\DB::raw("(2*(MATCH(evt.title) AGAINST('" . $against
                . "' IN BOOLEAN MODE)) + (MATCH(evt.description) AGAINST('" . $against
                . "' IN BOOLEAN MODE))) AS `rank`"))
            ->addSelect(\DB::raw('( 3959 * acos( cos( radians(' . $lat
                . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng
                . ') ) + sin( radians(' . $lat . ') ) * sin( radians( latitude ) ) ) ) as truedistance'))
            ->where(function ($where) {
                if (request()->isMethod('POST') && request()->has('last_time')) {
                    $where->where('news_feeds.created_at', '<', request()->get('last_time'));
                }
            })
            ->where(function ($whereAgainst) use ($against) {
                if ($against != '') {
                    $whereAgainst->orWhereRaw("MATCH(evt.title) AGAINST('".$against."' IN BOOLEAN MODE)")
                    ->orWhereRaw("MATCH(evt.description) AGAINST('".$against."' IN BOOLEAN MODE)");
                }
            })
            ->where('news_feeds.instances_id', '=', session('instanceId'))
            ->where('post_type', '=', 'TEvent')
            ->having('truedistance', '<=', $distance)
            ->having('rank', $compareRank, '0')
            ->orderBy('news_feeds.created_at', 'desc')
            ->take(10)
            ->get();

        $data = array();
        if (request()->has('last_time')) {
            $data['autoScroll'] = 1;
        } else {
            $data['autoScroll'] = 0;
        }
        $data['events'] = $events;

        return response()->json(array(
            'view' => $view = view('event.events-results', $data)->render(),
        ));
    }
}
