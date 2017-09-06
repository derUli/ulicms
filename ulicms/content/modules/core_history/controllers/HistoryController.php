<?php
class HistoryController extends Controller {
	public function doRestore() {
		if (isset ( $_GET ["version_id"] )) {
			$version_id = intval ( $_GET ["version_id"] );
			$rev = VCS::getRevisionByID ( $version_id );
			if ($rev) {
				VCS::restoreRevision ( $version_id );
			}
			Request::redirect ( ModuleHelper::buildActionURL ( "pages_edit", "page=" . $rev->content_id ) );
		}
	}
}