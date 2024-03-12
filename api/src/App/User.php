<?php

declare(strict_types=1);

namespace App;

use DateTime;

class User {
    /**
     * Construct one or more User objects from a database query result
     * NOTE: Assumes that rows are grouped by user id or all refer to the same user
     * @param array{
     *   id: int,
     *   name: string,
     *   email: string,
     *   phone_number: string,
     *   password_hash: string,
     *   day_of_week: ?string,
     *   start_hour: ?int,
     *   end_hour: ?int
     * }[] $rows
     * @return User[]
     */
    public static function fromRows(array $rows): array {
        $users = [];

        $current = new User();
        foreach ($rows as $row) {
            if (isset($current->userId) && $current->userId !== $row['id']) {
                $users[] = $current;
                $current = new User();
            }
            $current->userId = $row['id'];
            $current->userName = $row['name'];
            $current->email = $row['email'];
            $current->passwordHash = $row['password_hash'];
            $current->phoneNumber = $row['phone_number'];

            $day = $row['day_of_week'] ?? null;
            $start = $row['start_hour'] ?? null;
            $end = $row['end_hour'] ?? null;
            if ($day !== null && $start !== null && $end !== null) {
                $current->setAvailability(
                    DayOfWeek::from($day),
                    new TimeRange($start, $end)
                );
            }
        }
        $users[] = $current;

        return $users;
    }

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
