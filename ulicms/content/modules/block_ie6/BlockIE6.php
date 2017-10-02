<?php
class BlockIE6 extends Controller {
	public function beforeInit() {
		$ua = get_useragent ();
		if (preg_match ( '/\bmsie 6/i', $ua ) && ! preg_match ( '/\bopera/i', $ua )) {
			HTMLResult ( "IE6 is not supported.", 400 );
		}
	}
}