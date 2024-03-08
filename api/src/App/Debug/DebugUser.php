<?php

namespace App\Debug;

use Faker\Generator;

class DebugUser
{
    public static function createDummyUser(Generator $faker): \App\User
    {
        $user = new \App\User();
        // Normally a username would never include the password, but this is just dummy data
        // and we want to be able to log in as a dummy user
        $password = $faker->password();
        $user->userName = $faker->unique()->name() . ' password: ' . $password;
        $user->phoneNumber = $faker->unique()->e164PhoneNumber();
        $user->email = $faker->unique()->email();
        $user->passwordHash = password_hash($password, PASSWORD_DEFAULT);

        foreach (\App\DayOfWeek::cases() as $day) {
            if (rand(0, 100) > 50) {
                $start = $faker->numberBetween(9, 14);
                $end = $faker->numberBetween($start + 1, 17);

                $user->setAvailability($day, new \App\TimeRange($start, $end));
            }
        }

        return $user;
    }
}
