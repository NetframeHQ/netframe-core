<?php
namespace App;

use GuzzleHttp\Client as Guzzle;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class OutlookCalendar
{
    private $client;
    private $service = null;
    private $accessToken;

    public function getAuthUrl()
    {

        return 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id=' . env('ONEDRIVE_CLIENT_ID')
            . '&response_type=code&response_mode=query&scope=Calendars.ReadWrite%20offline_access'
            . '&redirect_uri=' . route('calendar.authorize', ['type'=>'outlook']);
    }

    public function auth($refresh_token = null, $access_token = null, $code = null)
    {
        if (isset($access_token)) {
            $this->access_token = $access_token;
        }
        if (isset($refresh_token)) {
            $this->refresh_token = $refresh_token;
        }
        $guzzle = new Guzzle();
        $url = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
        if (isset($code)) {
            $token = json_decode($guzzle->post($url, [
                'form_params' => [
                    'client_id' => env('ONEDRIVE_CLIENT_ID'),
                    'client_secret' => env('ONEDRIVE_CLIENT_SECRET'),
                    'redirect_uri' => route('calendar.authorize', ['type'=>'outlook']),
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                ],
            ])->getBody()->getContents());
            $this->access_token = $token->access_token;
            $this->refresh_token = $token->refresh_token;
        }
        if (isset($refresh_token) && !isset($access_token)) {
            $token = json_decode($guzzle->post($url, [
                'form_params' => [
                    'client_id' => env('ONEDRIVE_CLIENT_ID'),
                    'client_secret' => env('ONEDRIVE_CLIENT_SECRET'),
                    'redirect_uri' => route('calendar.authorize', ['type'=>'outlook']),
                    'refresh_token' => $refresh_token,
                    'grant_type' => 'refresh_token',
                ],
            ])->getBody()->getContents());
            $this->refresh_token = $token->refresh_token;
            $this->access_token = $token->access_token;
        }
        if (isset($this->access_token) || isset($access_token)) {
            $this->service = new Graph();
            $this->service->setAccessToken(isset($this->access_token) ? $this->access_token : $access_token);
        }
        if (isset($code)) {
            $events = $this->service->createRequest("GET", '/me/events')
                                ->setReturnType(Model\Event::class)
                                ->execute();

            return $events[0]->getOrganizer()->getEmailAddress()->getAddress() ?? 'Outlook';
        }
        return null;
    }

    public function getEvents($start, $end)
    {
        // $start = "01-05-2019"; $end = "31-05-2019";
        $events = null;
        $params = [
            "\$select" => "subject,start,end",
            "\$orderby" => "Start/DateTime",
            "startDateTime" => date_format(new \Datetime($start), 'Y-m-d\TH:i:s'),
            "endDateTime" => date_format(new \Datetime($end), 'Y-m-d\TH:i:s'),
        ];
        $uri = '/me/calendarview?'.http_build_query($params);
        try {
            $events = $this->service->createRequest("GET", $uri)
                                ->setReturnType(Model\Event::class)
                                ->execute();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->auth($this->refresh_token);
            $events = $this->service->createRequest("GET", $uri)
                                ->setReturnType(Model\Event::class)
                                ->execute();
        }
        // dd($events);
        $data = array();
        foreach ($events as $event) {
            $data[] = [
                'url'=>'#',
                'title'=>$event->getSubject(),
                'start'=> date_format(new \Datetime($event->getStart()->getDateTime()), "Y-m-d H:i:s"),
                'end'=>date_format(new \Datetime($event->getEnd()->getDateTime()), "Y-m-d H:i:s"),
                'color'=>'#ff5308'
            ];
        }
        return $data;
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
        $event = [
            "subject"=> $ss->title,
            "body"=> [
              "contentType"=> "TEXT",
              "content"=> $ss->description
            ],
            "start"=> [
                "dateTime"=> date_format(new \Datetime($start), 'Y-m-d\TH:i:s'),
                "timeZone"=> config('app.timezone')
            ],
            "end"=> [
                "dateTime"=> date_format(new \Datetime($end), 'Y-m-d\TH:i:s'),
                "timeZone"=> config('app.timezone')
            ],
        ];
        $uri = '/me/events';
        try {
            $event = $this->service->createRequest("POST", $uri)
                ->attachBody(json_encode($event))
                ->setReturnType(Model\Event::class)
                ->execute();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->auth($this->refresh_token);
            $event = $this->service->createRequest("POST", $uri)
                ->attachBody(json_encode($event))
                ->setReturnType(Model\Event::class)
                ->execute();
                // \Log::error("Error while sending event to outlook...");
        }
        return true;
    }
}
