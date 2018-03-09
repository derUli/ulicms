<?php
class GulliTicker extends MainClass {
	const MODULE_NAME = "gulli_ticker";
	public function render() {
		return Template::executeModuleTemplate ( GulliTicker::MODULE_NAME, "ticker.php" );
	}
}