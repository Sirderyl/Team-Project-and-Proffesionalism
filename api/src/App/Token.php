<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Utility class for issuing and verifying tokens
 * @author Kieran
 */
class Token
{
    // TODO: Settings class
    private const JWT_SECRET = 'T&=JIz;3sN<g@&4z?E[;"32[etAsCh';

    private function __construct()
    {
    }

    /**
     * Issue a token
     * @param int $userId
     */
    public static function issue(int $userId): string
    {
        $payload = [
            "iat" => time(),
            "nbf" => time(),
            // TODO: Settings class
            "exp" => time() + 3600,
            "iss" => $_SERVER['HTTP_HOST'],
            "sub" => $userId,
        ];

        return JWT::encode($payload, self::JWT_SECRET, 'HS256');
    }

    /**
     * Verify a token and get the user it was issued for
     * @param string $token
     */
    public static function verify(string $token): int
    {
        $payload = JWT::decode($token, new Key(self::JWT_SECRET, 'HS256'));

        return $payload->sub;
    }
}
