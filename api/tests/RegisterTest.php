<?php

use PHPUnit\Framework\TestCase;
use App\Debug;
use App\Database;
use App\Register;

class RegisterTest extends TestCase {
    private const BODY = [
        'name' => 'Bob',
        'email' => 'bob@example.com',
        'password' => 'Pa55w0rd!',
        'phone' => '123-456-7890'
    ];

    private Database\DatabaseInterface $database;
    private Register $register;

    protected function setUp(): void {
        $this->database = Debug\DebugDatabase::createTestDatabase();
        $this->register = new App\Register($this->database);
    }

    public function testParseBody(): void {
        $encoded = json_encode(self::BODY);
        // Should never happen, check to make PHPStan happy
        if ($encoded === false) throw new Exception('Failed to encode JSON');
        $parsed = $this->register->parseBody($encoded);

        $this->assertEquals(self::BODY, $parsed);
    }

    public function testExecute(): void {
        $result = $this->register->execute(self::BODY);

        $user = $this->database->users()->get(self::BODY['email']);
        $this->assertEquals(App\Token::verify($result['token']), $user->userId);
        $this->assertEquals(self::BODY['name'], $user->userName);
        $this->assertEquals(self::BODY['phone'], $user->phoneNumber);
        $this->assertEquals(self::BODY['email'], $user->email);
        $this->assertTrue(password_verify(self::BODY['password'], $user->passwordHash));
    }

    public function testFailIfCredentialsTaken(): void {
        $this->register->execute(self::BODY);
        $this->expectException(PDOException::class);
        $this->register->execute(self::BODY);
    }
}
