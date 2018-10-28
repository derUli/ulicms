<?php
use UliCMS\Exceptions\NotImplementedException;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{

    public static function splitHeaders($headers)
    {
        $header_array = array();
        $lines = normalizeLN($headers, "\n");
        $lines = explode("\n", $lines);
        foreach ($lines as $line) {
            $kv = explode(":", $line, 2);
            $kv = array_map("trim", $kv);
            $kv = array_filter($kv, "strlen");
            if (count($kv) == 2) {
                $header_array[trim($kv[0])] = trim($kv[1]);
            }
        }
        return $header_array;
    }

    public static function send($to, $subject, $message, $headers = "")
    {
        $mode = Settings::get("email_mode") ? Settings::get("email_mode") : EmailModes::INTERNAL;
        
        // UliCMS speichert seit UliCMs 9.0.1 E-Mails, die das System versendet hat
        // in der Datenbank
        $insert_sql = "INSERT INTO " . tbname("mails") . " (headers, `to`, subject, body) VALUES ('" . db_escape($headers) . "', '" . db_escape($to) . "', '" . db_escape($subject) . "', '" . db_escape($message) . "')";
        db_query($insert_sql);
        
        // Damit Umlaute auch im Betreff korrekt dargestellt werden, diese mit UTF-8 kodieren
        $subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
        
        // TODO: Hieraus einen Switch machen
        switch ($mode) {
            case EmailModes::INTERNAL:
            case EmailModes::PHPMAILER:
                return self::sendWithPHPMailer($to, $subject, $message, $headers, $mode);
                break;
                break;
            default:
                throw new NotImplementedException("E-Mail Mode \"$message\" not implemented.");
                break;
        }
    }

    public static function getPHPMailer($mode = EmailModes::INTERNAL)
    {
        $mailer = new PHPMailer();
        $mailer->SMTPDebug = 3;
        
        $mailer->Debugoutput = function ($str, $level) {
            $logger = LoggerRegistry::get("phpmailer_log");
            if ($logger) {
                $logger->debug($str);
            }
        };
        // If we use SMTP setup PHPMailer with a SMTP connection
        // else PHPMailer will use the mail() function of PHP.
        if ($mode == EmailModes::PHPMAILER) {
            $mailer->SMTPSecure = Settings::get("smtp_encryption");
            
            // disable verification of ssl certificates
            // this option makes the mail transfer insecure
            // use this only if it's unavoidable
            if (Settings::get("smtp_no_verify_certificate")) {
                $mailer->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
            }
            
            if (Settings::get("smtp_host")) {
                $mailer->isSMTP();
                $mailer->Host = Settings::get("smtp_host");
                $mailer->SMTPAuth = (Settings::get("smtp_auth") === "auth");
                if (Settings::get("smtp_user")) {
                    $mailer->Username = Settings::get("smtp_user");
                }
                if (Settings::get("smtp_password")) {
                    $mailer->Password = Settings::get("smtp_password");
                }
                $mailer->Port = Settings::get("smtp_port", "int");
            }
        }
        $mailer->XMailer = Settings::get("show_meta_generator") ? "UliCMS" : "";
        $mailer->CharSet = "UTF-8";
        
        $mailer = apply_filter($mailer, "php_mailer_instance");
        return $mailer;
    }

    public static function sendWithPHPMailer($to, $subject, $message, $headers = "", $mode = EmailModes::INTERNAL)
    {
        $headers = self::splitHeaders($headers);
        $headersLower = array_change_key_case($headers, CASE_LOWER);
        
        $mailer = self::getPHPMailer($mode);
        
        if (isset($headersLower["x-mailer"])) {
            $mailer->XMailer = $headersLower["x-mailer"];
        }
        $mailer->setFrom(StringHelper::isNotNullOrWhitespace($headers["From"]) ? $headers["From"] : Settings::get("email"));
        
        if (isset($headersLower["reply-to"])) {
            
            $mailer->addReplyTo($headersLower["reply-to"]);
        }
        $mailer->addAddress($to);
        $mailer->Subject = $subject;
        $mailer->isHTML(isset($headersLower["content-type"]) and startsWith($headersLower["content-type"], "text/html"));
        $mailer->Body = $message;
        
        $mailer = apply_filter($mailer, "php_mailer_send");
        return $mailer->send();
    }
}