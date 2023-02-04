<?php

namespace App\Helpers;

use \DateTimeZone;
use \DateTime;

class DateHelper
{
    public static function getTimezoneOffset($remote_tz, $origin_tz = null)
    {
        if ($origin_tz === null) {
            if (!is_string($origin_tz = date_default_timezone_get())) {
                return false; // A UTC timestamp was returned -- bail out!
            }
        }
        $origin_dtz = new DateTimeZone($origin_tz);
        $remote_dtz = new DateTimeZone($remote_tz);
        $origin_dt = new DateTime("now", $origin_dtz);
        $remote_dt = new DateTime("now", $remote_dtz);
        $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
        return $offset;
    }

    public static function getFullTimeZone($localTImezone = null)
    {
        $tzArray = [];
        foreach (timezone_identifiers_list() as $tz) {
            $this_tz = new DateTimeZone($tz);
            $now = new DateTime("now", $this_tz);
            $offset = $this_tz->getOffset($now);
            $offsetHours = $offset/3600;
            $userTimeZone = ($localTImezone == $tz) ? 1 : 0;
            $tzArray[] = [
                'offsetH' => $offsetHours,
                'timezone' => $tz,
                'offset' => $offset,
                'userTimeZone' => $userTimeZone,
            ];
        }
        sort($tzArray);
        return $tzArray;
    }

    public static function convertToLocalUTC($datetime, $forcedTimeZone = null)
    {
        $timeStamp = strtotime($datetime);

        //get time zone from ip
        if ($forcedTimeZone != null) {
            $timezone_identifiers = \DateTimeZone::listIdentifiers();
            $tz = $timezone_identifiers[$forcedTimeZone];
        } else {
            $tz = session('userTimezone');
        }

        $offset = \App\Helpers\DateHelper::getTimezoneOffset(config('app.timezone'), $tz);
        $localTimeStamp = $timeStamp - $offset;

        $utcReturn = [
            'datetime' => date("Y-m-d H:i:s", $localTimeStamp),
            'time' => date("H:i:s", $localTimeStamp),
        ];

        return $utcReturn;
    }

    public static function convertToUTC($datetime)
    {
        $timeStamp = strtotime($datetime);

        $offset = \App\Helpers\DateHelper::getTimezoneOffset('UTC', config('app.timezone'));
        $localTimeStamp = $timeStamp - $offset;

        $utcReturn = [
            'datetime' => date("Y-m-d H:i:s", $localTimeStamp),
            'time' => date("H:i:s", $localTimeStamp),
        ];

        return $utcReturn;
    }

    public static function convertFromLocalUTC($datetime, $return = 'default', $forcedTimeZone = null)
    {
        $timeStamp = strtotime($datetime);

        if ($forcedTimeZone == null) {
            $tz = session('userTimezone');
        } else {
            $tz = $forcedTimeZone;
        }


        $offset = \App\Helpers\DateHelper::getTimezoneOffset(config('app.timezone'), $tz);
        $localTimeStamp = $timeStamp + $offset;

        switch ($return) {
            case 'default':
                $utcReturn = [
                    'datetime' => date("Y-m-d H:i:s", $localTimeStamp),
                    'time' => date("H:i:s", $localTimeStamp),
                ];
                break;

            case 'datetime':
                $utcReturn = date("Y-m-d H:i:s", $localTimeStamp);
                break;

            case 'time':
                $utcReturn = date("H:i:s", $localTimeStamp);
                break;
        }

        return $utcReturn;
    }

    public static function feedDate($createdTime, $updatedTime = null)
    {
        if ($updatedTime != null && $createdTime < $updatedTime) {
            $refTime = $updatedTime;
            $refCU = 'updated_';
        } else {
            $refTime = $createdTime;
            $refCU = '';
        }
        $carbon = new \Carbon\Carbon();
        $timeStamp = strtotime($refTime);

        //if diff from now and date < 5 display xx ago
        $elementDate = new \Carbon\Carbon($refTime);
        $now = $carbon->now();
        $diffDate = $elementDate->diff($now);

        if ($diffDate->days < 5) {
            if ($diffDate->days > 0) {
                return trans('date.' . $refCU . 'preDate')
                    . ' ' . $diffDate->d
                    . ' ' . trans_choice('date.sufDays', $diffDate->d)
                    . ' ' . trans('date.sufDate');
            } else {
                $diffHours = $elementDate->diffInHours($now);
                if ($diffHours > 0) {
                    return trans('date.' . $refCU . 'preDate')
                        . ' ' . $diffDate->h
                        . ' ' . trans_choice('date.sufHours', $diffDate->h)
                        . ' ' . trans('date.sufDate');
                } elseif (strtotime($now)-strtotime($elementDate) > 60) {
                    return trans('date.' . $refCU . 'preDate')
                        . ' ' . $diffDate->i
                        . ' ' . trans_choice('date.sufMin', $diffDate->i)
                        . ' ' . trans('date.sufDate');
                } else {
                    return trans('date.' . $refCU . 'now');
                }
            }
        } else {
            $tz = session('userTimezone');

            $offset = \App\Helpers\DateHelper::getTimezoneOffset(config('app.timezone'), $tz);
            $localTimeStamp = $timeStamp + $offset;

            return date('d', $localTimeStamp)
                . ' ' . trans('date.shortMonth.' . date('n', $localTimeStamp))
                . ' ' . date('Y', $localTimeStamp)
                . ', ' . date('H:i', $localTimeStamp);
        }
    }

    public static function eventPartialDate($date, $time, $type)
    {
        $tz = session('userTimezone');

        $offset = \App\Helpers\DateHelper::getTimezoneOffset(config('app.timezone'), $tz);
        $timeStamp = strtotime($date.' '.$time) + $offset;

        switch ($type) {
            case 'month':
                return trans('date.shortMonth.'.date("n", $timeStamp));
                break;

            case 'day':
                return date("d", $timeStamp);
                break;
        }
    }

    public static function eventDate($date, $time, $date_end = null, $time_end = null)
    {
        $tz = session('userTimezone');

        $offset = \App\Helpers\DateHelper::getTimezoneOffset(config('app.timezone'), $tz);
        $timeStamp = strtotime($date . ' ' . $time) + $offset;

        // if all day long event return day
        if ($time == null) {
            $return = date('d', $timeStamp)
                . ' ' . trans('date.month.' . date('n', $timeStamp))
                . ' ' . date('Y', $timeStamp);
            if ($time != null) {
                $return .= ', ' . date('H.i', $timeStamp);
            }
            return $return;
        }

        // convert date in user datetime
        if ($time != null) {
            $timeStampStart = strtotime($date.' '.$time) + $offset;
            $date = date('Y-m-d', $timeStampStart);
        }
        if ($time_end != null) {
            if ($date_end != null) {
                $timeStampEnd = strtotime($date_end.' '.$time_end) + $offset;
            } else {
                $timeStampEnd = strtotime($date.' '.$time_end) + $offset;
            }
            $date_end = date('Y-m-d', $timeStampEnd);
            $time_end = date('H:i:s', $timeStampEnd);
        }

        if ($time_end != null && ($date_end == null || ($date == $date_end) )) {
            $return = date('d', $timeStamp)
                . ' ' . trans('date.month.' . date('n', $timeStamp))
                . ' ' . date('Y', $timeStamp);
            if ($time != null) {
                $return .= ', ' . trans('date.from.time') . ' ' . date('H.i', $timeStamp) . ' ';
            }
            if ($time_end != null) {
                $return .= trans('date.to.time') . ' ' . date('H.i', $timeStampEnd);
            }
        } elseif ($date_end != null) {
            $return = trans('date.from.date')
                . ' ' . date('d', $timeStamp)
                . ' ' . trans('date.month.' . date('n', $timeStamp))
                . ' ' . date('Y', $timeStamp);
            if ($time != null) {
                $return .= ", ".date("H.i", $timeStamp);
            }
            $return .= ' ' . trans('date.to.date')
                . ' ' . date('d', $timeStampEnd)
                . ' ' . trans('date.month.' . date('n', $timeStampEnd))
                . ' ' . date('Y', $timeStampEnd);
            if ($time_end != null) {
                $return .= ', ' . date('H.i', $timeStampEnd);
            }
        } else {
            $return = date('d', $timeStamp)
                .' '.trans('date.month.'.date('n', $timeStamp))
                .' '.date('Y', $timeStamp);
            if ($time != null) {
                $return .= ', ' . date('H.i', $timeStamp);
            }
        }
        return $return;
    }

    public static function messageDate($datetime)
    {
        $carbon = new \Carbon\Carbon();
        $timeStamp = strtotime($datetime);

        //if diff from now and date < 5 display xx ago
        $elementDate = new \Carbon\Carbon($datetime);
        $now = $carbon->now();
        $diffDate = $elementDate->diff($now);

        if ($diffDate->days < 5) {
            if ($diffDate->days > 0) {
                return trans('date.preDate')
                    . ' ' . $diffDate->d
                    . ' ' . trans_choice('date.sufDays', $diffDate->d)
                    . ' ' . trans('date.sufDate');
            } else {
                $diffHours = $elementDate->diffInHours($now);
                if ($diffHours > 0) {
                    return trans('date.preDate')
                        . ' ' . $diffDate->h
                        . ' ' . trans_choice('date.sufHours', $diffDate->d)
                        . ' ' . trans('date.sufDate');
                } elseif (strtotime($now)-strtotime($elementDate) > 60) {
                    return trans('date.preDate')
                        . ' ' . $diffDate->i
                        . ' ' . trans_choice('date.sufMin', $diffDate->i)
                        . ' ' . trans('date.sufDate');
                } else {
                    return trans('date.now');
                }
            }
        } else {
            $tz = session('userTimezone');

            $offset = \App\Helpers\DateHelper::getTimezoneOffset(config('app.timezone'), $tz);
            $localTimeStamp = $timeStamp + $offset;

            return date('d', $localTimeStamp)
                . ' ' . trans('date.shortMonth.' . date('n', $localTimeStamp))
                . ' ' . date('Y', $localTimeStamp);
        }
    }

    public static function xplorerDate($createdTime, $updatedTime = null)
    {
        if ($updatedTime != null && $createdTime < $updatedTime) {
            $refTime = $updatedTime;
            //$refCU = 'updated_';
            $refCU = '';
        } else {
            $refTime = $createdTime;
            $refCU = '';
        }
        $carbon = new \Carbon\Carbon();
        $timeStamp = strtotime($refTime);

        //if diff from now and date < 5 display xx ago
        $elementDate = new \Carbon\Carbon($refTime);
        $now = $carbon->now();
        $diffDate = $elementDate->diff($now);

        if ($diffDate->days < 5) {
            if ($diffDate->days > 0) {
                return trans('date.' . $refCU . 'preDate')
                    . ' ' . $diffDate->d
                    . ' ' . trans_choice('date.sufDays', $diffDate->d)
                    . ' ' . trans('date.' . $refCU . 'sufDate');
            } else {
                $diffHours = $elementDate->diffInHours($now);
                if ($diffHours > 0) {
                    return trans('date.' . $refCU . 'preDate')
                        . ' ' . $diffDate->h
                        . ' ' . trans_choice('date.sufHours', $diffDate->h)
                        . ' ' . trans('date.' . $refCU . 'sufDate');
                } elseif (strtotime($now)-strtotime($elementDate) > 60) {
                    return trans('date.' . $refCU . 'preDate')
                        . ' ' . $diffDate->i
                        . ' ' . trans_choice('date.sufMin', $diffDate->i)
                        . ' ' . trans('date.' . $refCU . 'sufDate');
                } else {
                    return trans('date.' . $refCU . 'now');
                }
            }
        } else {
            $tz = session('userTimezone');

            $offset = \App\Helpers\DateHelper::getTimezoneOffset(config('app.timezone'), $tz);
            $localTimeStamp = $timeStamp + $offset;

            return date('d', $localTimeStamp)
                . ' ' . trans('date.shortMonth.' . date('n', $localTimeStamp))
                . ' ' . date('Y', $localTimeStamp);
        }
    }
}
