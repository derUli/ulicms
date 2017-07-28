<?php
class SecurityHTTPHeaders extends Controller {
	public function beforeInit() {
		if (Request::isSSL ()) {
			header ( 'Strict-Transport-Security: max-age=16070400; includeSubDomains' );
		}
		header ( 'X-Frame-Options: SAMEORIGIN' );
		header ( 'X-XSS-Protection: 1; mode=block' );
		header ( 'X-Content-Type-Options: nosniff' );
		header ( "Referrer-Policy: no-referrer-when-downgrade" );
		
		// **PREVENTING SESSION HIJACKING**
		// Prevents javascript XSS attacks aimed to steal the session ID
		ini_set ( 'session.cookie_httponly', 1 );
		
		// **PREVENTING SESSION FIXATION**
		// Session ID cannot be passed through URLs
		ini_set ( 'session.use_only_cookies', 1 );
		
		// Uses a secure connection (HTTPS) if possible
		ini_set ( 'session.cookie_secure', 1 );
	}
}