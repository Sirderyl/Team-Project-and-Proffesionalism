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

    public function sendEmail(array $data): string {
        $recipientEmail = $data['email'];
        $recipientName = $data['name'];
        $activity = $data['activity'];
        $activityDetails = $data['activityDetails'];
        return $this->mailer->sendEmail($recipientEmail, $recipientName, $activity, $activityDetails);
    }
}
