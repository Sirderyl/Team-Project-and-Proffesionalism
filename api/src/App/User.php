<?php

declare(strict_types=1);

namespace App;

use DateTime;

class User {
    public string $userId;
    public string $userName;
    public array $availability;

    public string $phoneNumber;

    public string $email;
    public string $passwordHash;

    public function setAvailability(
    ?DateTime $monStart, ?DateTime $monEnd,
    ?DateTime $tueStart, ?DateTime $tueEnd,
    ?DateTime $wedStart, ?DateTime $wedEnd,
    ?DateTime $thuStart, ?DateTime $thuEnd,
    ?DateTime $friStart, ?DateTime $friEnd,
    ?DateTime $satStart, ?DateTime $satEnd,
    ?DateTime $sunStart, ?DateTime $sunEnd)
    {
        $this->availability = array(
            "Monday" => array("startTime" => $monStart, "endTime" => $monEnd),
            "Tuesday" => array("startTime" => $tueStart, "endTime" => $tueEnd),
            "Wednesday" => array("startTime" => $wedStart, "endTime" => $wedEnd),
            "Thursday" => array("startTime" => $thuStart, "endTime" => $thuEnd),
            "Friday" => array("startTime" => $friStart, "endTime" => $friEnd),
            "Saturday" => array("startTime" => $satStart, "endTime" => $satEnd),
            "Sunday" => array("startTime" => $sunStart, "endTime" => $sunEnd),
        );
    }



}
