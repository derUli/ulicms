<?php
class MailerTest extends PHPUnit_Framework_TestCase {
	public function testSplitHeaders() {
		$headers = "";
		$headers .= "From: info@company.com\n";
		$headers .= "Reply-To: reply@company.com\n";
		$headers .= ":Invalid Column\n";
		$headers .= "Invalid Column:\n";
		$headers .= "Invalid Column\n";
		$headers .= "Another Invalid Column\r\r\n\n";
		$headers .= "X-Mailer: My Cool Mailer";
		
		$parsed = Mailer::splitHeaders ( $headers );
		$this->assertEquals ( 3, count ( $parsed ) );
		$this->assertEquals ( "info@company.com", $parsed ["From"] );
		$this->assertEquals ( "reply@company.com", $parsed ["Reply-To"] );
		$this->assertEquals ( "My Cool Mailer", $parsed ["X-Mailer"] );
	}
	
	// TODO: Implement Unit Tests für Sending mails with all available mail delivery methods
	// Use fake a fake smtp server
}