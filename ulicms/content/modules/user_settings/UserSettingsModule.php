<?php
class UserSettingsModule extends Controller {
	public function uninstall() {
		$migrator = new DBMigrator ( "module/user_settings", ModuleHelper::buildModuleRessourcePath ( "user_settings", "sql/down" ) );
		$migrator->rollback ();
	}
}