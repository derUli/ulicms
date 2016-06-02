<?php
class ModuleHelper {
	public static function buildAdminURL($module, $suffix = null) {
		$url = "?action=module_settings&module=" . $module;
		if ($suffix !== null and ! empty ( $suffix )) {
			$url .= "&" . $suffix;
		}
		$url = rtrim ( $url, "&" );
		return $url;
	}
}