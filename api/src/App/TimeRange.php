<?php

namespace App;

/**
 * A time range in a single day
 * @phpstan-type TimeRangeArray array{start: float, end: float}
 * @author Kieran
 */
class TimeRange {
    /** @param TimeRangeArray $array */
    public static function fromArray(array $array): TimeRange {
        return new TimeRange($array['start'], $array['end']);
    }
    public float $start;
    public float $end;


    public function __construct(float $start, float $end) {
        if ($start < 0 || $start > 24) {
            throw new \InvalidArgumentException('Start time must be between 0 and 24');
        }
        if ($end < 0 || $end > 24) {
            throw new \InvalidArgumentException('End time must be between 0 and 24');
        }
        if ($start >= $end) {
            throw new \InvalidArgumentException('Start time must be before end time');
        }

        $this->start = $start;
        $this->end = $end;
    }

    /** @return TimeRangeArray */
    public function toArray(): array {
        return [
            'start' => $this->start,
            'end' => $this->end
        ];
    }
}
