<?php

declare(strict_types=1);

namespace App;

class UserActivity {
    public $userId;
    public $activityId;
    public $rating;
    public $startTime;

    public function __construct($userId, $activityId, $rating) {
        $this->userId = $userId;
        $this->activityId = $activityId;
        $this->rating = $rating;
    }
}