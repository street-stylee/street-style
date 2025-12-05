<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';

class EmailHelper {

    public static function enviar(
        string $para_email, 
        string $para_nome, 
        string $assunto, 
        string $corpo_html, 
        string $email_resposta = null,
        string $nome_resposta = null
    ): bool {
        
        $mail = new PHPMailer(true);

        $smtp_host = 'smtp.gmail.com';
        $smtp_user = 'streetstyle.ufc@gmail.com';
        $smtp_pass = 'idcy ehgw nlgp bgtl';

        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = $smtp_host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp_user;
            $mail->Password   = $smtp_pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom($smtp_user, 'Street Style (Notificações)');

            $mail->addAddress($para_email, $para_nome);

            if ($email_resposta && $nome_resposta) {
                $mail->addReplyTo($email_resposta, $nome_resposta);
            }

            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body    = $corpo_html;
            $mail->AltBody = strip_tags($corpo_html);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer falhou: " . $mail->ErrorInfo);
            return false;
        }
    }
}