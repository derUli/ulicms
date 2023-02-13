<?php

use PHPMailer\PHPMailer\PHPMailer;
use App\Constants\EmailModes;
use PHPMailer\PHPMailer\SMTP;

class MailerTest extends \PHPUnit\Framework\TestCase
{
    private $initialSettings = [];

    protected function setUp(): void
    {
        LoggerRegistry::register(
            "phpmailer_log",
            new Logger(Path::resolve("ULICMS_LOG/phpmailer_log"))
        );

        $settingKeys = [
            "email_mode",
            "smtp_host",
            "smtp_port",
            "smtp_no_verify_certificate",
            "smtp_password",
            "smtp_no_verify_certificate",
            "smtp_auth"
        ];

        $this->initialSettings = [];
        foreach ($settingKeys as $key) {
            $this->initialSettings[$key] = Settings::get($key);
        }
    }

    protected function tearDown(): void
    {
        LoggerRegistry::unregister("phpmailer_log");

        foreach ($this->initialSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testSplitHeaders()
    {
        $headers = '';
        $headers .= "From: info@company.com\n";
        $headers .= "Reply-To: reply@company.com\n";
        $headers .= ":Invalid Column\n";
        $headers .= "Invalid Column:\n";
        $headers .= "Invalid Column\n";
        $headers .= "Another Invalid Column\r\r\n\n";
        $headers .= "X-Mailer: My Cool Mailer";

        $parsed = Mailer::splitHeaders($headers);
        $this->assertEquals(3, count($parsed));
        $this->assertEquals("info@company.com", $parsed["From"]);
        $this->assertEquals("reply@company.com", $parsed["Reply-To"]);
        $this->assertEquals("My Cool Mailer", $parsed["X-Mailer"]);
    }

    public function testGetPHPMailer()
    {
        $mailer = Mailer::getPHPMailer();
        $this->assertInstanceOf(PHPMailer::class, $mailer);
        $this->assertTrue(in_array($mailer->SMTPSecure, array(
            "",
            "tls",
            "ssl"
        )));
        $this->assertEquals(Settings::get("show_meta_generator") ? "UliCMS" : "", $mailer->XMailer);

        $this->assertFalse($mailer->SMTPAuth);
    }

    public function testGetPHPMailerWithEmailMode()
    {
        $mailer = $this->setUpPhpMailer();
        $this->assertEquals(
            [
                "ssl" => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ],
            $mailer->SMTPOptions
        );

        $this->assertEquals("smtp.foo.bar", $mailer->Host);
        $this->assertEquals("mailuser", $mailer->Username);
        $this->assertEquals("993", $mailer->Port);
        $this->assertEquals("secret", $mailer->Password);

        $this->assertTrue(in_array($mailer->SMTPSecure, [
            "",
            "tls",
            "ssl"
        ]));
    }

    protected function setUpPhpMailer(): PHPMailer
    {
        Settings::set("smtp_auth", "1");
        Settings::set("smtp_no_verify_certificate", "1");
        Settings::set("smtp_host", "smtp.foo.bar");
        Settings::set("smtp_user", "mailuser");
        Settings::set("smtp_password", "secret");
        Settings::set("smtp_port", "993");

        $mailer = Mailer::getPHPMailer(EmailModes::PHPMAILER);
        $this->assertInstanceOf(PHPMailer::class, $mailer);
        $this->assertTrue(in_array($mailer->SMTPSecure, array(
            "",
            "tls",
            "ssl"
        )));

        $mailer->SMTPDebug = SMTP::DEBUG_LOWLEVEL;
        return $mailer;
    }

    public function testEmailModes()
    {
        $this->assertEquals("internal", EmailModes::INTERNAL);
        $this->assertEquals("phpmailer", EmailModes::PHPMAILER);
    }

    public function testSendWithPHPMailer()
    {
        $headers = "X-Mailer: Der Gerät\n";
        $headers .= "Reply-To: antwort@adresse.invalid\n";
        $headers .= "Content-Type: text/html";

        $this->assertIsBool(
            Mailer::sendWithPHPMailer("john@doe.invalid", "Testmail", "Hallo John!", $headers)
        );
    }

    public function testGetMailLogger()
    {
        $logFunction = Mailer::getMailLogger();

        $this->assertIsCallable($logFunction);
        $logFunction("Hallo Welt", SMTP::DEBUG_CONNECTION);
    }
}
