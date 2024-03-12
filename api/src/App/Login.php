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

    /**
     * Throw an exception indicating an invalid username OR password
     */
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
    public function execute(?string $email, ?string $password): array
    {
        if ($email === null || $password === null) {
            throw new \InvalidArgumentException('Missing username or password');
        }

        $user = null;
        try {
            $user = $this->database->users()->get($email);
        } catch (Database\NotFoundException $e) {
            $this->throwInvalidCredentials();
        }

        if (!password_verify($password, $user->passwordHash)) {
            $this->throwInvalidCredentials();
        }

        return [
            'token' => Token::issue($user->userId)
        ];
    }
}
