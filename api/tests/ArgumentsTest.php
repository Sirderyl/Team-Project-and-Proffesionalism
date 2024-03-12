<?php

use PHPUnit\Framework\TestCase;
use App\Arguments;

class ArgumentsTest extends TestCase {
    public function testValidJson(): void {
        $args = Arguments::parseJson('{"a": 1, "b": 2}');
        $this->assertEquals($args, ['a' => 1, 'b' => 2]);
    }

    public function testFailIfInvalidJson(): void {
        $this->expectException(InvalidArgumentException::class);
        Arguments::parseJson('{"a": 1, "b": 2');
    }

    public function testFailIfNotObjectOrArray(): void {
        $this->expectException(InvalidArgumentException::class);
        Arguments::parseJson('"blah"');
    }

    public function testGetStringOk(): void {
        $args = ['a' => 'hello', 'b' => 'world'];
        $this->assertEquals(Arguments::getString($args, 'a'), 'hello');
    }

    public function testGetStringFailIfMissing(): void {
        $this->expectException(InvalidArgumentException::class);
        Arguments::getString([], 'a');
    }

    public function testGetStringFailIfNotString(): void {
        $this->expectException(InvalidArgumentException::class);
        Arguments::getString(['a' => 1], 'a');
    }
}
