<?php

declare(strict_types=1);

namespace App\Utils;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\EmailModes;
use App\Registries\LoggerRegistry;
use Closure;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Settings;

/**
 * Send Mails
 */
class Mailer {
    /**
     * Split mail header string to array
     * @param string $headers
     * @return array
     */
    public static function splitHeaders(string $headers): array {
        $header_array = [];
        $lines = normalizeLN($headers, "\n");
        $lines = explode("\n", $lines);
        foreach ($lines as $line) {
            $kv = explode(':', $line, 2);
            $kv = array_map('trim', $kv);
            $kv = array_filter($kv, 'strlen');
            if (count($kv) == 2) {
                $header_array[trim($kv[0])] = trim($kv[1]);
            }
        }
        return $header_array;
    }

    /**
     * Send email
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $headers
     * @return bool
     */
    public static function send(
        string $to,
        string $subject,
        string $message,
        string $headers = ''
    ): bool {
        // Use PHP Mail or SMTP
        $mode = Settings::get('email_mode') ?: EmailModes::INTERNAL;

        // UliCMS speichert seit UliCMS 9.0.1 E-Mails, die das System versendet hat
        // in der Datenbank
        // TODO: Make a method for this sql statement
        $insert_sql = 'INSERT INTO ' . tbname('mails') .
                " (headers, `to`, subject, body) VALUES ('" .
                db_escape($headers) . "', '" . db_escape($to) . "', '" .
                db_escape($subject) . "', '" . db_escape($message) . "')";
        db_query($insert_sql);

        return self::sendWithPHPMailer(
            $to,
            $subject,
            $message,
            $headers,
            $mode
        );
    }

    /**
     * Get mail logger
     * @return Closure
     */
    public static function getMailLogger(): Closure {
        return static function($str, $level) {
            $logger = LoggerRegistry::get('phpmailer_log');
            if ($logger) {
                $logger->debug($str);
            }
        };
    }

    /**
     * Initiate PHPMailer
     * @param string $mode
     * @return PHPMailer|null
     */
    public static function getPHPMailer(
        string $mode = EmailModes::INTERNAL
    ): ?PHPMailer {
        $mailer = new PHPMailer();
        $mailer->SMTPDebug = SMTP::DEBUG_CONNECTION;

        $mailer->Debugoutput = self::getMailLogger();

        // If we use SMTP setup PHPMailer with a SMTP connection
        // else PHPMailer will use the mail() function of PHP.
        if ($mode == EmailModes::PHPMAILER) {
            $mailer = self::setPHPMailerAttributes($mailer);
        }

        $mailer->XMailer = Settings::get('show_meta_generator') ? 'UliCMS' : '';
        $mailer->CharSet = 'UTF-8';
        $mailer->Encoding = 'quoted-printable';

        $mailer = apply_filter($mailer, 'php_mailer_instance');
        return $mailer;
    }

    /**
     * Send EMail with PHPMailer
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $headers
     * @param string $mode
     * @return bool
     */
    public static function sendWithPHPMailer(
        string $to,
        string $subject,
        string $message,
        string $headers = '',
        string $mode = EmailModes::INTERNAL
    ): bool {
        $headers = self::splitHeaders($headers);
        $headersLower = array_change_key_case($headers, CASE_LOWER);

        $mailer = self::getPHPMailer($mode);

        if (isset($headersLower['x-mailer'])) {
            $mailer->XMailer = $headersLower['x-mailer'];
        }
        $from = isset($headers['From']) && ! empty($headers['From']) ?
                $headers['From'] : Settings::get('email');
        $mailer->setFrom($from);

        if (isset($headersLower['reply-to'])) {
            $mailer->addReplyTo($headersLower['reply-to']);
        }
        $mailer->addAddress($to);
        $mailer->Subject = $subject;
        $mailer->isHTML(
            isset(
                $headersLower['content-type']) && str_starts_with(
                    $headersLower['content-type'],
                    'text/html'
                )
        );
        $mailer->Body = $message;

        $mailer = apply_filter($mailer, 'php_mailer_send');
        return $mailer->send();
    }

    /**
     * Apply attributes to PHPMailer
     * @param PHPMailer $mailer
     * @return PHPMailer
     */
    protected static function setPHPMailerAttributes(PHPMailer $mailer): PHPMailer {
        $mailer->SMTPSecure = Settings::get('smtp_encryption');

        // Disable verification of ssl certificates
        // this option makes the mail transfer insecure
        // use this only if it's unavoidable
        if (Settings::get('smtp_no_verify_certificate')) {
            $mailer->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
        }

        if (Settings::get('smtp_host')) {
            $mailer->isSMTP();
            $mailer->Host = Settings::get('smtp_host');
            $mailer->SMTPAuth = (Settings::get('smtp_auth') === 'auth');
            if (Settings::get('smtp_user')) {
                $mailer->Username = Settings::get('smtp_user');
            }
            if (Settings::get('smtp_password')) {
                $mailer->Password = Settings::get('smtp_password');
            }
            $mailer->Port = Settings::get('smtp_port', 'int');
        }
        return $mailer;
    }
}
