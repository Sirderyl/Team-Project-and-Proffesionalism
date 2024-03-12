<?php

use PHPUnit\Framework\TestCase;
use App\TimeRange;

class TimeRangeTest extends TestCase {
    public function testOk(): void {
        $time = new TimeRange(9, 17);
        $this->assertEquals($time->toArray(), [
            'start' => 9,
            'end' => 17
        ]);
    }

    public function testBothMidnight(): void {
        $time = new TimeRange(0, 24);
        $this->assertEquals($time->toArray(), [
            'start' => 0,
            'end' => 24
        ]);
    }

    public function testStartBelowZero(): void {
        $this->expectException(InvalidArgumentException::class);
        new TimeRange(-1, 17);
    }

    public function testStartAbove24(): void {
        $this->expectException(InvalidArgumentException::class);
        new TimeRange(25, 17);
    }

    public function testEndBelowZero(): void {
        $this->expectException(InvalidArgumentException::class);
        new TimeRange(9, -1);
    }

    public function testEndAbove24(): void {
        $this->expectException(InvalidArgumentException::class);
        new TimeRange(9, 25);
    }

    public function testEndBeforeStart(): void {
        $this->expectException(InvalidArgumentException::class);
        new TimeRange(17, 9);
    }
}
