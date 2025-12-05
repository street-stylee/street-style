<?php

namespace App\Services;

require_once APP_PATH . '/Helpers/PHPMailer/Exception.php';
require_once APP_PATH . '/Helpers/PHPMailer/PHPMailer.php';
require_once APP_PATH . '/Helpers/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class EmailService
{
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587;
    const SMTP_USERNAME = 'streetstyle.ufc@gmail.com';
    const SMTP_PASSWORD = 'tnqz phuj qkbx uqzb'; 
    const SENDER_EMAIL = 'streetstyle.ufc@gmail.com';
    const SENDER_NAME  = 'Street Style - Suporte';

    public static function sendEmail(string $toEmail, string $subject, string $body): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = self::SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = self::SMTP_USERNAME;
            $mail->Password   = self::SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = self::SMTP_PORT;

            $mail->setFrom(self::SENDER_EMAIL, self::SENDER_NAME);
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            return $mail->send();

        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: " . $mail->ErrorInfo);
            return false;
        }
    }
}
