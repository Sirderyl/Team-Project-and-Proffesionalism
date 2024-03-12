<?php

use PHPUnit\Framework\TestCase;
use App\Token;

class TokenTest extends TestCase {
    public function testIssueIsValid(): void {
        $token = Token::issue(1);
        $this->assertEquals(Token::verify($token), 1);
    }

    public function testVerifyFailsIfNotSignedBySameKey(): void {
        $this->expectException(\Firebase\JWT\SignatureInvalidException::class);

        // Source: https://jwt.io/
        // Not issued by this server, so it should be rejected
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c';

        Token::verify($token);
    }
}
