<?php
class TestMailSender {
	private $to = null;
	private $headers = null;
	public function __construct($to = null, $headers = null) {
		if (StringHelper::isNotNullOrWhitespace ( $to )) {
			$this->to = $to;
		} else {
			$this->to = Settings::get ( "email" );
		}
		if (StringHelper::isNotNullOrWhitespace ( $headers )) {
			$this->headers = $headers;
		} else {
			$this->headers = "From: " . Settings::get ( "email" ) . "\r\n". "Content-Type: text/plain; charset=UTF-8";
		}
	}
	public function getTo() {
		return $this->to;
	}
	public function getHeaders() {
		return $this->headers;
	}
	public function setTo($val) {
		$this->to = StringHelper::isNotNullOrWhitespace ( $val ) ? $val : null;
	}
	public function setHeaders($val) {
		$this->headers = StringHelper::isNotNullOrWhitespace ( $val ) ? $val : null;
	}
	public function send() {
		Mailer::send ( $this->getTo (), get_translation ( "test_mail_subject" ), get_translation ( "test_mail_body", array (
				"%domain%" => get_domain () 
		) ), $this->getHeaders () );
	}
}