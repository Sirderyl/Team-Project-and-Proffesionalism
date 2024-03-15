<?php

namespace App;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

/**
 * Utility class for issuing and verifying tokens
 * @author Kieran
 */
class Token
{
    // TODO: Settings class
    private const JWT_SECRET = 'T&=JIz;3sN<g@&4z?E[;"32[etAsCh';

    /**
     * Issue a token
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
     * @return int The user ID the token is for
     */
    public static function verify(string $token): int
    {
        $payload = JWT::decode($token, new Key(self::JWT_SECRET, 'HS256'));

        return $payload->sub;
    }

    /**
     * Assert that the provided user ID matches the one given in the token
     * @throws SignatureInvalidException If the token is for a different user
     */
    public static function checkAuthMatchesUser(string $token, int $userId): void
    {
        $issued = self::verify($token);

        if ($issued != $userId) {
            throw new SignatureInvalidException("Token does not match user ID");
        }
    }
}
