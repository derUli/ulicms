<?php

use PHPMailer\PHPMailer\PHPMailer;
use UliCMS\Constants\EmailModes;
use UliCMS\Exceptions\NotImplementedException;

class MailerTest extends \PHPUnit\Framework\TestCase {

	private $initialEmailMode;

	public function setUp() {
		$this->initialEmailMode = Settings::get("email_mode");
	}

	public function tearDown() {
		Settings::set("email_mode", $this->initialEmailMode);
	}

	public function testSplitHeaders() {
		$headers = "";
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

	public function testGetPHPMailer() {
		$mailer = Mailer::getPHPMailer();
		$this->assertInstanceOf(PHPMailer::class, $mailer);
		$this->assertTrue(in_array($mailer->SMTPSecure, array(
			"",
			"tls",
			"ssl"
		)));
		$this->assertEquals(Settings::get("show_meta_generator") ? "UliCMS" : "", $mailer->XMailer);
	}

	public function testEmailModes() {
		$this->assertEquals("internal", EmailModes::INTERNAL);
		$this->assertEquals("phpmailer", EmailModes::PHPMAILER);
	}

	public function testSendThrowsNotImplementedException() {
		Settings::set("email_mode", "brieftaube");

		$this->expectException(NotImplementedException::class);
		Mailer::send("john@doe.invalid", "Testmail", "Hallo John!");
	}

	public function testSendWithPHPMailer() {
		$headers = "X-Mailer: Der Gerät\n";
		$headers .= "Reply-To: antwort@adresse.invalid\n";
		$headers .= "Content-Type: text/html";

		$this->assertIsBool(
				Mailer::sendWithPHPMailer("john@doe.invalid", "Testmail", "Hallo John!", $headers)
		);
	}

}
