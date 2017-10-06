<?php
class Authorization {
	public $type = null;
	public $user = null;
	public $password = null;
	public function __construct($header = null) {
		if ($header) {
			$this->fromHeader ( $header );
		}
	}
	public function fromHeader($header) {
		$header = trim ( $header );
		$typeAndToken = explode ( " ", $header );
		$type = $typeAndToken [0];
		$token = base64_decode ( $typeAndToken [1] );
		$usernamePassword = base64_decode ( $token );
		$usernamePassword = explode ( ":", $usernamePassword );
		$this->user = $usernamePassword [0];
		// Config-Array Wert "plain_text_password_auth_types" auswerten.
		// wenn $type darin enthalten ist, new PlainTextPasswort() statt new Password() verwenden
		$authenticator = ControllerRegistry::get ( "HttpAuthenticator" );
		$cfg = $authenticator->getConfig ();
		if (isset ( $cfg ["plain_text_password_auth_types"] ) and is_array ( $cfg ["plain_text_password_auth_types"] ) and in_array ( $type, $cfg ["plain_text_password_auth_types"] )) {
			$this->password = new PlainTextPassword ( $usernamePassword [1] );
		} else {
			$this->password = new Password ( $usernamePassword [1] );
		}
	}
	public function __toString() {
		$token = base64_encode ( $this->user . ":" . $this->password->value );
		return $this->type . " " . $token;
	}
}