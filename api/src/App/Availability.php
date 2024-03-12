<?php

namespace App;

/**
 * User availability specification for a given week
 * @author Filip
 */
class Availability {
    
    public string $userId;
    public DayOfWeek $day;
    public TimeRange $time;
}
