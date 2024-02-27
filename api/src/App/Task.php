<?php

declare(strict_types=1);

namespace App;

use DateTime;

class Task {
    
    public int $taskId;
    public string $taskName;
    public DateTime $startTime;
    public DateTime $endTime;

}