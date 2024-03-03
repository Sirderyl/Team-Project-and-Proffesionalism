<?php

declare(strict_types=1);

namespace App;

use DateTime;

class User {
    public string $userId;
    public string $userName;

    /**
     * @var array<string, array{startTime: float, endTime: float}>
     * @see DayOfWeek
     */
    public array $availability;

    public string $phoneNumber;

    public string $email;
    public string $passwordHash;

    // FIXME: 14 parameters! This is a code smell. Take a day and time range instead.
    public function setAvailability(
        float $monStart, float $monEnd,
        float $tueStart, float $tueEnd,
        float $wedStart, float $wedEnd,
        float $thuStart, float $thuEnd,
        float $friStart, float $friEnd,
        float $satStart, float $satEnd,
        float $sunStart, float $sunEnd): void
    {
        $this->availability = array(
            DayOfWeek::Monday->value => array("startTime" => $monStart, "endTime" => $monEnd),
            DayOfWeek::Tuesday->value => array("startTime" => $tueStart, "endTime" => $tueEnd),
            DayOfWeek::Wednesday->value => array("startTime" => $wedStart, "endTime" => $wedEnd),
            DayOfWeek::Thursday->value => array("startTime" => $thuStart, "endTime" => $thuEnd),
            DayOfWeek::Friday->value => array("startTime" => $friStart, "endTime" => $friEnd),
            DayOfWeek::Saturday->value => array("startTime" => $satStart, "endTime" => $satEnd),
            DayOfWeek::Sunday->value => array("startTime" => $sunStart, "endTime" => $sunEnd),
        );
    }



}
