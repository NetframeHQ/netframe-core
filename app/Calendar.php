<?php

namespace App;

use App\TEvent;
use App\Project;
use App\House;
use App\Community;
use App\User;

class Calendar
{
    public $jsonData;
    private $start;
    private $end;
    private $startArray;
    private $endArray;

    public function __construct($start, $end)
    {
        // format dates and times
        $startArray = explode(' ', $start);
        $endArray = explode(' ', $end);

        if (!isset($startArray[1])) {
            $startArray[1] = '00:00:00';
        }

        if (!isset($endArray[1])) {
            $endArray[1] = '00:00:00';
        }

        $this->start = $start;
        $this->end = $end;
        $this->startArray = $startArray;
        $this->endArray = $endArray;
    }

    private function timeline($profile_type = null, $profile_id = null)
    {
        $start = $this->start;
        $end = $this->end;
        $startArray = $this->startArray;
        $endArray = $this->endArray;

        if ($profile_type == 'user') {
            $user = User::find($profile_id);
            $userId = $user->id;
        } else {
            $userId = Auth()->guard('web')->user()->id;
        }
        $events = TEvent::select('events.*')
            ->leftJoin('news_feeds', function ($join) {
                $join->on('news_feeds.true_author_id', '=', 'events.author_id')
                ->on('news_feeds.true_author_type', '=', 'events.author_type')
                ->on('news_feeds.post_id', '=', 'events.id');
            })
            ->leftJoin('subscriptions as sub', function ($joinS) {
                $joinS->on('sub.profile_type', '=', 'news_feeds.author_type')
                ->on('sub.profile_id', '=', 'news_feeds.author_id');
            })
            ->leftJoin('events_has_friends as ehf', function ($joinE) {
                $joinE->on('ehf.events_id', '=', 'news_feeds.post_id')
                ->where('news_feeds.post_type', '=', 'TEvent');
            })
            ->where('news_feeds.post_type', '=', 'App\\TEvent')
            ->where('news_feeds.instances_id', '=', session('instanceId'))
            ->where(function ($where) use ($userId) {
                $where->orWhere(function ($whereS) use ($userId) {
                    $whereS->where('sub.users_id', '=', $userId)
                    ->where('news_feeds.confidentiality', '>=', 'sub.confidentiality');
                })
                ->orWhere(function ($whereE) use ($userId) {
                    $whereE->where('ehf.users_id', '=', $userId);
                })
                ->orWhere(function ($userEvents) use ($userId) {
                    $userEvents->where('news_feeds.author_id', '=', $userId)
                    ->where('news_feeds.author_type', '=', 'App\\User');
                });
            })
            ->where(function ($loadDates) use ($start, $end, $startArray, $endArray) {
                $loadDates->where(function ($startQ) use ($start, $end) {
                    $startQ->where('start_date', '>=', $start)
                    ->where('start_date', '<', $end);
                })
                ->orWhere('end_date', '<', $end)
                ->orWhere(function ($overDates) use ($start, $end) {
                    $overDates->where('start_date', '<', $start)
                    ->where('end_date', '>', $end);
                })
                ->orWhere(function ($noTimeStart) use ($startArray, $endArray) {
                    $noTimeStart->whereNull('start_date')
                    ->where('date', '>=', $startArray[0])
                    ->where('date', '<', $endArray[0]);
                })
                ->orWhere(function ($noTimeEnd) use ($endArray) {
                    $noTimeEnd->whereNull('end_date')
                    ->where('date_end', '<', $endArray[0]);
                });
            })
            ->where('events.active', '=', '1')
            ->groupBy('events.id')
            ->get();

        return $events;
    }

    private function allEvents($profile_type = null, $profile_id = null)
    {
        $start = $this->start;
        $end = $this->end;
        $startArray = $this->startArray;
        $endArray = $this->endArray;

        $events = TEvent::select('events.*')
            ->leftJoin('news_feeds', function ($join) {
                $join->on('news_feeds.true_author_id', '=', 'events.author_id')
                ->on('news_feeds.true_author_type', '=', 'events.author_type')
                ->on('news_feeds.post_id', '=', 'events.id');
            })
            ->where('news_feeds.confidentiality', '=', 1)
            ->where('news_feeds.post_type', '=', 'App\\TEvent')
            ->where('news_feeds.instances_id', '=', session('instanceId'))
            ->where('events.confidentiality', '=', '1')
            ->where('events.active', '=', '1')
            ->where(function ($loadDates) use ($start, $end, $startArray, $endArray) {
                $loadDates->where(function ($startQ) use ($start, $end) {
                    $startQ->where('start_date', '>=', $start)
                    ->where('start_date', '<', $end);
                })
                ->orWhere('end_date', '<', $end)
                ->orWhere(function ($overDates) use ($start, $end) {
                    $overDates->where('start_date', '<', $start)
                    ->where('end_date', '>', $end);
                })
                ->orWhere(function ($noTimeStart) use ($startArray, $endArray) {
                    $noTimeStart->whereNull('start_date')
                    ->where('date', '>=', $startArray[0])
                    ->where('date', '<', $endArray[0]);
                })
                ->orWhere(function ($noTimeEnd) use ($endArray) {
                    $noTimeEnd->whereNull('end_date')
                    ->where('date_end', '<', $endArray[0]);
                });
            })
            ->groupBy('events.id')
            ->get();

            return $events;
    }

    private function profile($profile_type, $profile_id)
    {
        if ($profile_type == 'user') {
            return $this->timeline('user', $profile_id);
        }

        $start = $this->start;
        $end = $this->end;
        $startArray = $this->startArray;
        $endArray = $this->endArray;

        $profileModel = Profile::gather($profile_type);
        $profile = $profileModel::find($profile_id);

        $events = TEvent::select('events.*')
            ->leftJoin('news_feeds', function ($join) {
                $join->on('news_feeds.true_author_id', '=', 'events.author_id')
                ->on('news_feeds.true_author_type', '=', 'events.author_type')
                ->on('news_feeds.post_id', '=', 'events.id');
            })
            ->where('news_feeds.author_id', '=', $profile->id)
            ->where('news_feeds.author_type', '=', get_class($profile))
            ->where('news_feeds.post_type', '=', 'App\\TEvent')
            ->where(function ($loadDates) use ($start, $end, $startArray, $endArray) {
                $loadDates->where(function ($startQ) use ($start, $end) {
                    $startQ->where('start_date', '>=', $start)
                    ->where('start_date', '<', $end);
                })
                ->orWhere('end_date', '<', $end)
                ->orWhere(function ($overDates) use ($start, $end) {
                    $overDates->where('start_date', '<', $start)
                    ->where('end_date', '>', $end);
                })
                ->orWhere(function ($noTimeStart) use ($startArray, $endArray) {
                    $noTimeStart->whereNull('start_date')
                    ->where('date', '>=', $startArray[0])
                    ->where('date', '<', $endArray[0]);
                })
                ->orWhere(function ($noTimeEnd) use ($endArray) {
                    $noTimeEnd->whereNull('end_date')
                    ->where('date_end', '<', $endArray[0]);
                });
            })
            ->groupBy('events.id')
            ->get();

        return $events;
    }

    /*
     * return json of events reletad to a type : allEvents, timeline, profile, user
     */
    public function getEvents($type, $profile_type = null, $profile_id = null)
    {
        $dataCalendar = $this->$type($profile_type, $profile_id);

        $events = [];

        foreach ($dataCalendar as $eventDate) {
            $startEvent = $eventDate->date.' '.$eventDate->time;
            $startEvent = \App\Helpers\DateHelper::convertFromLocalUTC($startEvent)['datetime'];

            $color = '#ffffff';

            // get owner profile
            $profile = $eventDate->posts()->first()->author;
            switch (class_basename($profile)) {
                case 'Project':
                    $color = "#4AB348";
                    break;

                case 'User':
                    $color = "#7C58C5";
                    break;

                case 'Community':
                    $color = "rgba(var(--nf-accentColor), 1)";
                    break;

                case 'House':
                    $color = "#3C78EE";
                    break;
            }

            if ($eventDate->date_end == null) {
                $endEvent = \Carbon\Carbon::parse($startEvent)->addHour();
            } else {
                $endEvent = $eventDate->date_end.' '.$eventDate->time_end;
            }
            $endEvent = \App\Helpers\DateHelper::convertFromLocalUTC($endEvent)['datetime'];
            if ($eventDate->time == null) {
                $events[] = [
                    'url' => $eventDate->getUrl(),
                    'title' => html_entity_decode($eventDate->title),
                    'start' => $eventDate->date,
                    'end' => $eventDate->date_end,
                    'color' => $color,
                    'allDay' => true,
                ];
            } else {
                $events[] = [
                    'url' => $eventDate->getUrl(),
                    'title' => html_entity_decode($eventDate->title),
                    'start' => $startEvent,
                    'end' => $endEvent,
                    'color' => $color,
                    'allDay' => false,
                ];
            }
        }

        return json_encode($events);
    }
}
