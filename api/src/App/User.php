<?php

declare(strict_types=1);

namespace App;

use DateTime;

class User {
    public int $userId;
    public string $userName;

    /**
     * @var array<string, TimeRange>
     * @see DayOfWeek
     */
    public array $availability = [];

    public string $phoneNumber;

    public string $email;
    public string $passwordHash;

    public function setAvailability(DayOfWeek $day, TimeRange $time): void
    {
        $this->availability[$day->value] = $time;
    }



}
