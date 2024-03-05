<?php

namespace App;

/**
 * A time range in a single day
 * @author Kieran
 */
class TimeRange {
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
}
