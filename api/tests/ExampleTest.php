<?php

use PHPUnit\Framework\TestCase;

final class ExampleTest extends TestCase
{
    public function testTrue(): void
    {
        $this->assertTrue(true);
    }

    public function testFail(): void
    {
        $this->assertTrue(false);
    }
}
