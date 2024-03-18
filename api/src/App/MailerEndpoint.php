<?php

namespace App;

/**
 * Implementation of /user/{email}/{name} endpoint
 * This will later be /user/{id}
 * @author Filip
 */
class MailerEndpoint {
    private Mailer $mailer;

    public function __construct(Mailer $mailer) {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $recipientEmail, string $recipientName): string {
        return $this->mailer->sendEmail($recipientEmail, $recipientName);
    }
}
