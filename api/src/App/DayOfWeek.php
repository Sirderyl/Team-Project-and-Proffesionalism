<?php

namespace App;

enum DayOfWeek: string {
    case Monday = 'Monday';
    case Tuesday = 'Tuesday';
    case Wednesday = 'Wednesday';
    case Thursday = 'Thursday';
    case Friday = 'Friday';
    case Saturday = 'Saturday';
    case Sunday = 'Sunday';

    /**
     * Convert an int to a DayOfWeek, starting from 0 for Monday
     * No toIndex, as this is only intended for generating test data
     */
    static function fromIndex(int $index): DayOfWeek {
        return match ($index) {
            0 => self::Monday,
            1 => self::Tuesday,
            2 => self::Wednesday,
            3 => self::Thursday,
            4 => self::Friday,
            5 => self::Saturday,
            6 => self::Sunday,
            default => throw new \InvalidArgumentException("Invalid index: $index"),
        };
        }
        static function fromString(string $string) : DayOfWeek {
            return match($string) {
                "Monday" => self::Monday,
                "Tuesday" => self::Tuesday,
                "Wednesday" => self::Wednesday,
                "Thursday" => self::Thursday,
                "Friday" => self::Friday,
                "Saturday" => self::Saturday,
                "Sunday" => self::Sunday,
                default => throw new \InvalidArgumentException("Invalid string: $string"),
            };
        }
}
