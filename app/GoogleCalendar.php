<?php

namespace App;

class GoogleCalendar
{
    private $client;
    private $service = null;

    public function __construct()
    {
        $this->client = new \Google_Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(route('calendar.authorize', ['type'=>'google']));
        $this->client->addScope('https://www.googleapis.com/auth/calendar.events'
          . ' https://www.googleapis.com/auth/userinfo.email');
        $this->client->setApprovalPrompt("force");
        $this->client->setAccessType(env('GOOGLE_ACCESS_TYPE'));
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function auth($refresh_token = null, $access_token = null, $code = null)
    {
        if (isset($access_token)) {
            $this->access_token = $access_token;
        }
        if (isset($refresh_token)) {
            $this->refresh_token = $refresh_token;
        }

        if (isset($code)) {
            $this->client->authenticate($code);
            $this->access_token = $this->client->getAccessToken()['access_token'];
            $this->refresh_token = $this->client->getRefreshToken();
        }

        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->refresh_token);
            $this->access_token = $this->client->getAccessToken()['access_token'];
            $this->refresh_token = $this->client->getRefreshToken();
        }

        if (isset($this->access_token) || isset($access_token)) {
            $this->client->setAccessToken(isset($this->access_token) ? $this->access_token : $access_token);
            $this->service = new \Google_Service_Calendar($this->client);
        }
        if (isset($code)) {
            $oauth2 = new \Google_Service_Oauth2($this->client);
            return $oauth2->userinfo->get()->email;
        }return null;
    }

    public function getEvents($start, $end)
    {
        // $start = "01-05-2019"; $end = "31-05-2019";
        $parameters = ['maxResults' => 100,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date_format(new \Datetime($start), 'Y-m-d\TH:i:s\Z'),
            'timeMax' => date_format(new \Datetime($end), 'Y-m-d\TH:i:s\Z'),
        ];

        $events = $this->service->events->listEvents("primary", $parameters)->items;

        $data = array();
        foreach ($events as $event) {
            $data[] = [
                'url'=>'#',
                'title'=>$event->summary,
                'start'=>$event->start->dateTime,
                'end'=>$event->end->dateTime,
                'color'=> '#4285f4'
            ];
        }
        return $data;
    }

    public function export()
    {
        $event = new \Google_Service_Calendar_Event(array(
          'summary' => 'Google I/O 2015',
          'location' => '800 Howard St., San Francisco, CA 94103',
          'description' => 'A chance to hear more about Google\'s developer products.',
          'start' => array(
            'dateTime' => '2015-05-28T09:00:00-07:00',
            'timeZone' => 'America/Los_Angeles',
          ),
          'end' => array(
            'dateTime' => '2015-05-28T17:00:00-07:00',
            'timeZone' => 'America/Los_Angeles',
          ),
          'recurrence' => array(
            'RRULE:FREQ=DAILY;COUNT=2'
          ),
          'attendees' => array(
            array('email' => 'lpage@example.com'),
            array('email' => 'sbrin@example.com'),
          ),
          'reminders' => array(
            'useDefault' => false,
            'overrides' => array(
              array('method' => 'email', 'minutes' => 24 * 60),
              array('method' => 'popup', 'minutes' => 10),
            ),
          ),
        ));

        $calendarId = 'primary';
        $event = $this->service->events->insert($calendarId, $event);
        printf('Event created: %s\n', $event->htmlLink);
    }

    public function send($ss)
    {
        $start = $ss->date.(' '.$ss->time ?? '');
        $end = $ss->date_end;
        if (isset($end)) {
            $end = $end.(' '.$ss->time_end ?? '');
        } elseif (isset($ss->time_end)) {
            $end = $ss->date.' '.$ss->time_end;
        } else {
            $timezone = new \DateTimeZone(config('app.timezone'));
            $offset   = $timezone->getOffset(new \DateTime)/3600;
            $end = $ss->date.' '.(23-$offset).':59:59';
        }
        $params = array(
            'summary' => $ss->title,
            'description' => $ss->description,
            'start' => array(
                'dateTime' => date_format(new \Datetime($start), 'Y-m-d\TH:i:s\Z'),
                'timeZone' => config('app.timezone'),
            ),
            'end' => array(
                'dateTime' => date_format(new \Datetime($end), 'Y-m-d\TH:i:s\Z'),
                'timeZone' => config('app.timezone'),
            )
        );
        // if(!isset($end)){
        //     $params['endTimeUnspecified'] = true;
        //     $params['end']['dateTime'] = date_format(date_add(
        //         new \Datetime($ss->date),date_interval_create_from_date_string('1 day')), 'Y-m-d\TH:i:s\Z');
        //     $params['end']['date'] = date_format(date_add(
        //         new \Datetime($ss->date),date_interval_create_from_date_string('1 day')), 'Y-m-d');
        // }
        $event = new \Google_Service_Calendar_Event($params);
        $event = $this->service->events->insert('primary', $event);
        // \Log::info($params);
        return true;
    }
}
