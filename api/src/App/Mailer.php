<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

class Mailer
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->Port = 587;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'newcastlevolunteering@gmail.com';
        $this->mail->Password = 'diawvwzkdlqmkqwo';
        $this->mail->setFrom('newcastlevolunteering@gmail.com', 'Newcastle Volunteering');
    }

    public function sendEmail($recipientEmail, $recipientName, $activity, $activityDetails) {
        $this->mail->addAddress($recipientEmail, $recipientName);
        $this->mail->isHTML(true);
        $this->mail->Subject = 'You have signed up for ' . $activity . '!';
        $this->mail->Body = 'Hi ' . $recipientName . ', you have signed up for ' . $activity . '.
        Below are the details of the activity: <br><br> ' . $activityDetails . '<br><br> Thank you for signing up!
        <br><br> Regards, <br> Newcastle Volunteering Team';
        $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients.';

        if (!$this->mail->send()) {
            return 'Message could not be sent. Mailer Error: ' . $this->mail->ErrorInfo;
        } else {
            return 'Message has been sent';
        }
    }
}
