<?php
/**
 * lib/mailer.php
 * ZPanel PHPMailer wrapper
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer libraries
require_once __DIR__ . '/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';

/**
 * Initialize PHPMailer
 */
function init_mailer(array $config): PHPMailer {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config['mail']['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['mail']['username'];
        $mail->Password   = $config['mail']['password'];
        $mail->SMTPSecure = $config['mail']['encryption'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['mail']['port'] ?? 587;
        $mail->Hostname   = $config['mail']['hostname'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';

        // From / Reply-to
        $mail->setFrom(
            $config['mail']['from_email'],
            $config['mail']['from_name']
        );
        if (!empty($config['mail']['reply_email'])) {
            $mail->addReplyTo(
                $config['mail']['reply_email'],
                $config['mail']['reply_name'] ?? $config['mail']['from_name']
            );
        }

        // Debug
        $mail->SMTPDebug = $config['mail']['debug'] ?? 0;
        $mail->CharSet   = 'UTF-8';

    } catch (Exception $e) {
        error_log("Mailer init failed: {$e->getMessage()}");
    }

    return $mail;
}

/**
 * Send Mail Wrapper
 */
function send_mail(array $config, string $to, string $subject, string $body, bool $isHtml = true): bool {
    $mail = init_mailer($config);

    try {
        $mail->addAddress($to);
        $mail->isHTML($isHtml);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: {$e->getMessage()}");
        return false;
    }
}

/**
 * Backward compatibility alias
 */
function sendMail($to, $subject, $body, $isHtml = true): bool {
    global $config;
    return send_mail($config, $to, $subject, $body, $isHtml);
}
