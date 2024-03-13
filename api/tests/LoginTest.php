<?php

use PHPUnit\Framework\TestCase;
use App\Database;
use App\Debug;

class LoginTest extends TestCase {
    private Database\DatabaseInterface $database;
    private Faker\Generator $faker;
    private App\Login $login;
    private App\User $user;
    private string $password;

    protected function setUp(): void {
        $this->faker = Faker\Factory::create();
        $this->database = Debug\DebugDatabase::createTestDatabase();
        $this->login = new App\Login($this->database);

        [$this->user, $this->password] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($this->user);
    }

    public function testOk(): void {
        $result = $this->login->execute($this->user->email, $this->password);
        $this->assertEquals(App\Token::verify($result['token']), $this->user->userId);
        $this->assertEquals($result['userId'], $this->user->userId);
    }

    public function testEmailIsCaseInsensitive(): void {
        $result = $this->login->execute(strtoupper($this->user->email), $this->password);
        $this->assertEquals(App\Token::verify($result['token']), $this->user->userId);
        $this->assertEquals($result['userId'], $this->user->userId);
    }

    public function testFailsIfNoEmail(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->login->execute(null, 'password');
    }

    public function testFailsIfNoPassword(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->login->execute('email', null);
    }

    public function testFailsIfWrongPassword(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->login->execute($this->user->email, 'wrong password');
    }

    public function testFailsIfWrongEmail(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->login->execute('wrong email', $this->password);
    }
}
