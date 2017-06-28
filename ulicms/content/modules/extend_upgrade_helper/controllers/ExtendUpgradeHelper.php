<?php
class ExtendUpgradeHelper extends Controller {
	public function getModules() {
		$result = array ();
		$modules = getAllModules ();
		foreach ( $modules as $module ) {
			if (getModuleMeta ( $module, "source" ) == "extend") {
				$xtendModule = new ExtendModule ();
				$xtendModule->name = $module;
				$xtendModule->version = getModuleMeta ( $module, "version" );
				$xtendModule->url = "https://extend.ulicms.de/" . $module . ".html";
				$result [] = $xtendModule;
			}
		}
		return $result;
	}
}