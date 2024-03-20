<?php

declare(strict_types=1);

namespace App;

class Rating {
    public $userId;
    public $activityId;
    public $rating;

    public function __construct($userId, $activityId, $rating) {
        $this->userId = $userId;
        $this->activityId = $activityId;
        $this->rating = $rating;
    }
}