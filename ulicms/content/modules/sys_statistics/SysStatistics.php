<?php
class SysStatistics extends Controller {
	private $moduleName = "sys_statistics";
	public function settings() {
		$language = Request::getVar ( "language" );
		$countByType = array ();
		$types = get_available_post_types ();
		
		foreach ( $types as $type ) {
			if ($language) {
				$query = Database::pQuery ( "select count(id) as amount from `{prefix}content` where type = ? and language = ?", array (
						$type,
						$language 
				), true );
			} else {
				$query = Database::pQuery ( "select count(id) as amount from `{prefix}content` where type = ?", array (
						$type 
				), true );
			}
			$result = Database::fetchObject ( $query );
			$countByType [$type] = $result->amount;
		}
		
		if ($language) {
			$query = Database::pQuery ( "select count(id) as amount from `{prefix}banner` where language = ?", array (
					$language 
			), true );
		} else {
			$query = Database::query ( "select count(id) as amount from `{prefix}banner`", true );
		}
		
		$result = Database::fetchObject ( $query );
		$countByType ["advertisements"] = $result->amount;
		
		$query = Database::pQuery ( "select count(id) as amount from `{prefix}users`", array (
				$language 
		), true );
		
		$result = Database::fetchObject ( $query );
		
		$countByType ["users"] = $result->amount;
		
		$query = Database::pQuery ( "select count(id) as amount from `{prefix}groups`", array (
				$language 
		), true );
		
		$result = Database::fetchObject ( $query );
		
		$countByType ["groups"] = $result->amount;
		
		$countByType ["installed_modules"] = count ( getAllModules () );
		$countByType ["embed_modules"] = count ( ModuleHelper::getAllEmbedModules () );
		$manager = new ModuleManager ();
		$countByType ["enabled_modules"] = count ( $manager->getEnabledModuleNames () );
		$countByType ["disabled_modules"] = count ( $manager->getDisabledModuleNames () );
		$countByType ["installed_designs"] = count ( getThemeList () );
		
		Viewbag::set ( "count_by_type", $countByType );
		
		return Template::executeModuleTemplate ( $this->moduleName, "info.php" );
	}
	public function getSettingsHeadline() {
		return get_translation ( "system_statistics" );
	}public function getSettingsLinkText() {
		return get_translation ( "system_statistics" );
	}
}