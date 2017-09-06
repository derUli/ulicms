<?php
class HistoryController extends Controller {
	public function doRestore() {
		if (isset ( $_GET ["do_restore_version"] )) {
			$do_restore_version = intval ( $_GET ["do_restore_version"] );
			$rev = VCS::getRevisionByID ( $do_restore_version );
			if ($rev) {
				VCS::restoreRevision ( $do_restore_version );
			}
			Request::redirect ( ModuleHelper::buildActionURL ( $action, "&page=" . $rev->content_id ) );
		}
	}
}