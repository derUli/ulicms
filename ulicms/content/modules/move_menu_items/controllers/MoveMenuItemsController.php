<?php
class MoveMenuItemsController extends Controller {
	private $moduleName = "move_menu_items";
	public function getSettingsHeadline() {
		return get_translation ( "move_menu_items" );
	}
	public function getSettingsLinkText() {
		return $this->getSettingsHeadline ();
	}
	protected function moveEntries($from, $to) {
		$sql = "update {prefix}content set menu = ? where menu = ?";
		$args = array (
				$to,
				$from 
		);
		Database::pQuery ( $sql, $args, true );
	}
	public function settings() {
		ViewBag::set ( "done", false );
		if (Request::isPost ()) {
			$move_from = Request::getVar ( "move_from" );
			$move_to = Request::getVar ( "move_to" );
			if (StringHelper::isNotNullOrWhitespace ( $move_from ) and StringHelper::isNotNullOrEmpty ( $move_to )) {
				$this->_moveEntries ( $move_from, $move_to );
				ViewBag::set ( "affected_rows", Database::getAffectedRows () );
				ViewBag::set ( "done", true );
			}
		}
		return Template::executeModuleTemplate ( $this->moduleName, "settings.php" );
	}
}