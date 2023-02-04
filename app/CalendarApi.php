<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use App\GoogleCalendar;
use App\OutlookCalendar;

class CalendarApi
{
    // protected $table = "calendar_apis";

    const GOOGLE = 0;
    const OUTLOOK = 1;

    public $type;
    public $refresh_token;
    public $access_token;
    public $email;

    public function __construct($array = [])
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getCalendar()
    {
        $calendar = null;
        switch ($this->type) {
            case self::GOOGLE:
                $calendar = new GoogleCalendar();
                break;
            case self::OUTLOOK:
                $calendar = new OutlookCalendar();
                break;
        }
        return $calendar;
    }

    public function auth($save = true)
    {
        $calendar = $this->getCalendar();
        $email = $calendar->auth($this->refresh_token, $this->access_token, $this->code);
        $this->refresh_token = $calendar->refresh_token;
        $this->access_token = $calendar->access_token;
        if (isset($this->code)) {
            unset($this->code);
        }
        if (isset($email)) {
            return $email;
        }
        return $calendar;
    }

    public function getEvents($start = null, $end = null)
    {
        $calendar = $this->getCalendar();
        $calendar->auth($this->refresh_token, $this->access_token);
        return $calendar->getEvents($start, $end);
    }

    public function export()
    {
        $calendar = $this->getCalendar();
        $calendar->auth($this->refresh_token, $this->access_token);
        return $calendar->export();
    }

    public function send($event)
    {
        $calendar = $this->getCalendar();
        $calendar->auth($this->refresh_token, $this->access_token);
        return $calendar->send($event);
    }

    public static function find($type, $email)
    {
        $user = auth()->guard('web')->user();
        $calendars = $user->getParameter("calendars_api");
        if ($calendars != null) {
            $calendars = json_decode($calendars);
            foreach ($calendars as $calendar) {
                if ($calendar->type == $type && $calendar->email == $email) {
                    $returnCalendar = new CalendarApi();
                    $returnCalendar->type = $calendar->type;
                    $returnCalendar->refresh_token = $calendar->refresh_token;
                    $returnCalendar->access_token = $calendar->access_token;
                    $returnCalendar->email = $calendar->email;
                    return $returnCalendar;
                }
            }
        }
    }

    public function save()
    {
        $api = ["type" => $this->type,
            "refresh_token" => $this->refresh_token,
            "access_token" => $this->access_token,
            "email" => $this->email];
        $user = auth()->guard('web')->user();
        $param = $user->getParameter("calendars_api");
        if (isset($param)) {
            $param = json_decode($param, true);
            $param[] = $api;
        } else {
            $param = [$api];
        }
        $user->setParameter("calendars_api", json_encode($param));
    }
}
