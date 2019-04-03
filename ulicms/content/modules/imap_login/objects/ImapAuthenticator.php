<?php
class ImapAuthenticator {
	private $cfg;
	public function __construct($cfg) {
		$this->cfg = $cfg;
	}
	public function authenticate($username, $password) {
		if (! isset ( $this->cfg ["imap_mailbox"] )) {
			throw new InvalidArgumentException ( "imap_mailbox is not set" );
		}
		return @imap_open ( $this->cfg ["imap_mailbox"], $username, $password ) !== false;
	}
}