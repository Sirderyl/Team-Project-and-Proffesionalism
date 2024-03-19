<?php

declare(strict_types=1);

namespace App;

class Rating {
    public int $userId;
    public int $activityId;
    public int $rating;

    public function __construct(int $userId, int $activityId, int $rating) {
        $this->userId = $userId;
        $this->activityId = $activityId;
        $this->rating = $rating;
    }
}
