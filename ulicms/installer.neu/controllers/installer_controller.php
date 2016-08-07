<?php
class Installer {
	public static function getTitle($step) {
		if (isset ( $_REQUEST ["step"] ) and ! empty ( $_REQUEST ["step"] )) {
			$step = intval ( $_REQUEST ["step"] );
		} else {
			$step = 1;
		}
		return constant ( "TRANLATION_TITLE_STEP_" . $step );
	}
}