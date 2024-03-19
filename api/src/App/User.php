<?php

declare(strict_types=1);

namespace App;

use DateTime;

class User {
    /**
     * Construct a user from a row of data
     * Availability is a JSON array with the following format:
     * Will be a single object with null values if the user is not available on any day
     * array<{day: ?string, ?start: float, end: ?float}>
     * @param array{
     *   id: int,
     *   name: string,
     *   email: string,
     *   phone_number: string,
     *   password_hash: string,
     *   is_manager: int,
     *   availability: string,
     *   day_of_week: ?string,
     * } $row
     * @return User
     */
    public static function fromRow(array $row): User {
        $user = new User();
        $user->userId = $row['id'];
        $user->userName = $row['name'];
        $user->email = $row['email'];
        $user->passwordHash = $row['password_hash'];
        $user->phoneNumber = $row['phone_number'];
        $user->isManager = $row['is_manager'] !== 0;

        /** @var array<array{day: ?string, start: ?float, end: ?float}> $availability */
        $availability = json_decode($row['availability'], true);
        if ($availability !== null) {
            foreach ($availability as $day) {
                if ($day['day'] !== null && $day['start'] !== null && $day['end'] !== null) {
                    $user->setAvailability(
                        DayOfWeek::from($day['day']),
                        new TimeRange($day['start'], $day['end'])
                    );
                }
            }
        }

        return $user;
    }

    public int $userId;

    // Determined at query time based on if the user is manager of any organization
    public bool $isManager = false;

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
