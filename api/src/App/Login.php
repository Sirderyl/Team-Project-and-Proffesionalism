<?php

namespace App;

/**
 * Implementation of /user/login
 */
class Login
{
    private readonly Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database)
    {
        $this->database = $database;
    }

    private function throwInvalidCredentials(): never
    {
        throw new \InvalidArgumentException('Invalid username or password');
    }

    /**
     * Execute the endpoint
     * @return array{
     *   'token': string
     * }
     */
    public function execute(): array
    {
        $email = $_SERVER['PHP_AUTH_USER'] ?? null;
        $pass = $_SERVER['PHP_AUTH_PW'] ?? null;

        if ($email === null || $pass === null) {
            throw new \InvalidArgumentException('Missing username or password');
        }

        $user = null;
        try {
            $user = $this->database->users()->get($email);
        } catch (Database\NotFoundException $e) {
            $this->throwInvalidCredentials();
        }

        if (!password_verify($pass, $user->passwordHash)) {
            throw new \InvalidArgumentException('Invalid username or password');
        }

        return [
            'token' => Token::issue($user->userId)
        ];
    }
}
