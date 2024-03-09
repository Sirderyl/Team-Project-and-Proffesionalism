<?php

declare(strict_types=1);

namespace App;

use DateTime;

class Task {
    
    public int $activityId;
    public string $activityName;
    public DateTime $startTime;
    public DateTime $endTime;

}