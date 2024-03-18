<?php

namespace App\Debug;

use Faker\Generator;

class DebugUser
{
    /**
     * Create a dummy user for testing purposes
     * If email or password is null, they will be randomly generated
     * @return array{0: \App\User, 1: string}
     */
    public static function createDummyUser(Generator $faker, ?string $email = null, ?string $password = null): array
    {
        $email ??= $faker->unique()->email();
        $password ??= $faker->password();

        $user = new \App\User();
        $user->userName = $faker->unique()->name();
        $user->phoneNumber = $faker->unique()->e164PhoneNumber();
        $user->email = $email;
        $user->passwordHash = password_hash($password, PASSWORD_DEFAULT);

        foreach (\App\DayOfWeek::cases() as $day) {
            if (rand(0, 100) > 50) {
                $start = $faker->numberBetween(9, 14);
                $end = $faker->numberBetween($start + 1, 17);

                $user->setAvailability($day, new \App\TimeRange($start, $end));
            }
        }

        return [$user, $password];
    }
}
