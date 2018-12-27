<?php
use UliCMS\Exceptions\NotImplementedException;
class PackageController extends Controller {
	const MODULE_NAME = "core_package_manager";
	public function getModuleInfo() {
		$name = stringOrNull ( Request::getVar ( "name", null, "str" ) );
		if (! $name) {
			return TextResult ( get_translation ( "not_found" ) );
		}
		$model = new ModuleInfoViewModel ();
		$model->name = $name;
		$model->version = getModuleMeta ( $name, "version" );
		$model->manufacturerName = getModuleMeta ( $name, "manufacturer_name" );
		$model->manufacturerUrl = getModuleMeta ( $name, "manufacturer_url" );
		$model->source = getModuleMeta ( $name, "source" );
		$model->customPermissions = is_array ( getModuleMeta ( $name, "custom_acl" ) ) ? getModuleMeta ( $name, "custom_acl" ) : array ();
		$model->adminPermission = getModuleMeta ( $name, "admin_permission" );
		natcasesort ( $model->customPermissions );
		ViewBag::set ( "model", $model );
		$html = Template::executeModuleTemplate ( self::MODULE_NAME, "packages/info/module.php" );
		HTMLResult ( $html );
	}
	public function getThemeInfo() {
		$name = stringOrNull ( Request::getVar ( "name", null, "str" ) );
		if (! $name) {
			return TextResult ( get_translation ( "not_found" ) );
		}
		$model = new ThemeInfoViewModel ();
		$model->name = $name;
		$model->version = getThemeMeta ( $name, "version" );
		$model->manufacturerName = getThemeMeta ( $name, "manufacturer_name" );
		$model->manufacturerUrl = getThemeMeta ( $name, "manufacturer_url" );
		$model->source = getThemeMeta ( $name, "source" );
		$model->disableFunctions = is_array ( getThemeMeta ( $name, "disable_functions" ) ) ? getThemeMeta ( $name, "disable_functions" ) : array ();
		natcasesort ( $model->disableFunctions );
		ViewBag::set ( "model", $model );
		$html = Template::executeModuleTemplate ( self::MODULE_NAME, "packages/info/theme.php" );
		HTMLResult ( $html );
	}
	public function uninstallModule() {
		throw new NotImplementedException ();
	}
	public function uninstallTheme() {
		throw new NotImplementedException ();
	}
}