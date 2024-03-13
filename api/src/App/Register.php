<?php

namespace App;

/**
 * Implementation of /user/register
 * @author Kieran
 * @phpstan-type RegisterBody array{
 *  name: string,
 *  email: string,
 *  password: string,
 *  phone: string
 * }
 *
 * @phpstan-type RegisterResponse array{
 *  token: string
 * }
 */
class Register
{
    private readonly Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database)
    {
        $this->database = $database;
    }

    /**
     * Parse the HTTP request body
     * @param string $body
     * @return RegisterBody
     */
    public function parseBody(string $body): array
    {
        $data = Arguments::parseJson($body);

        return [
            'name' => Arguments::getString($data, 'name'),
            'email' => Arguments::getString($data, 'email'),
            'password' => Arguments::getString($data, 'password'),
            'phone' => Arguments::getString($data, 'phone')
        ];
    }

    /**
     * Execute the endpoint
     * @param RegisterBody $body
     * @return RegisterResponse
     */
    public function execute(array $body): array
    {
        $user = new User();
        $user->userName = $body['name'];
        $user->phoneNumber = $body['phone'];
        $user->email = $body['email'];
        $user->passwordHash = password_hash($body['password'], PASSWORD_DEFAULT);

        $this->database->users()->create($user);

        return [
            'token' => Token::issue($user->userId),
            'userId' => $user->userId,
        ];
    }
}
