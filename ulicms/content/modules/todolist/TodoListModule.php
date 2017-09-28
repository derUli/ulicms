<?php
class TodoListModule extends Controller {
	private $moduleName = "todolist";
	public function getSettingsLinkText() {
		return get_translation ( "open" );
	}
	public function getSettingsHeadline() {
		return get_translation ( "todolist" );
	}
	public function settings() {
		return Template::executeModuleTemplate ( $this->moduleName, "list.php" );
	}
	public function addItem() {
		$acl = new ACL ();
		if ($acl->hasPermission ( getModuleMeta ( $this->moduleName, "admin_permission" ) ) and Request::getVar ( "title" )) {
			$item = new TodoListItem ();
			$item->setTitle ( Request::getVar ( "title" ) );
			$item->setUserId ( get_user_id () );
			$item->save ();
			ViewBag::set ( "item", $item );
			HTMLResult ( Template::executeModuleTemplate ( $this->moduleName, "item.php" ) );
		}
		HTMLResult ( get_translation ( "no_permissions" ), 403 );
	}
	public function checkItem() {
		if (! get_user_id ()) {
			HTMLResult ( get_translation ( "no_permissions" ), 403 );
		}
		$acl = new ACL ();
		if (! $acl->hasPermission ( getModuleMeta ( $this->moduleName, "admin_permission" ) )) {
			HTMLResult ( get_translation ( "no_permissions" ), 403 );
		}		
		$item = new TodoListItem ( Request::GetVar ( "id" ) );
		if ($item->getUserId () == get_user_id ()) {
			$item->setDone ( Request::getVar ( "done" ) );
			$item->save ();
			HTMLResult ( get_translation ( "ok" ), 200 );
		}
		HTMLResult ( get_translation ( "no_permissions" ), 403 );
	}
}