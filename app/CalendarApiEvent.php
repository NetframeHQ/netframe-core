<?php

namespace App;

use App\GoogleCalendar;

class CalendarApiEvent
{
    
    public function __construct($array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }
}
