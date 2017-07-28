<?php
class SecurityHTTPHeaders extends Controller {
	public function beforeInit() {
		if (Request::isSSL ()) {
			header ( 'Strict-Transport-Security: max-age=16070400; includeSubDomains' );
		}
		header ( 'X-Frame-Options: SAMEORIGIN' );
		header ( 'X-XSS-Protection: 1; mode=block' );
		header ( 'X-Content-Type-Options: nosniff' );
	}
}