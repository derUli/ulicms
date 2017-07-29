<?php
class MetadataViewer extends Controller {
	private $moduleName = "metadata_viewer";
	public function getSettingsHeadline() {
		return "Metadata Viewer";
	}
	public function getSettingsLinkText() {
		return get_translation ( "show_metadata" );
	}
	public function settings() {
		return Template::executeModuleTemplate ( $this->moduleName, "list.php" );
	}
	public function show() {
		$module = Request::getVar ( "module" );
		$theme = Request::getVar ( "theme" );
		if ($module) {
			$path = ModuleHelper::buildModuleRessourcePath ( basename ( $this->moduleName ), "metadata.json" );
			if (file_exists ( $path )) {
				ViewBag::set ( "title", $module );
				ViewBag::set ( "content", file_get_contents ( $path ) );
			}
		} else if ($theme) {
			$path = getTemplateDirPath ( basename ( $theme ) ) . "metadata.json";
			if (file_exists ( $path )) {
				ViewBag::set ( "title", "theme-" . $theme );
				ViewBag::set ( "content", file_get_contents ( $path ) );
			}
		}
	}
}