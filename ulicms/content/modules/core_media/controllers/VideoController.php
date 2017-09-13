<?php
class VideoController extends Controller {
	public function deletePost() {
		$query = db_query ( "select ogg_file, webm_file, mp4_file from " . tbname ( "videos" ) . " where id = " . intval ( $_REQUEST ["delete"] ) );
		if (db_num_rows ( $query ) > 0) {
			// OGG
			$result = db_fetch_object ( $query );
			$filepath = ULICMS_ROOT . "/content/videos/" . basename ( $result->ogg_file );
			if (! empty ( $result->ogg_file ) and is_file ( $filepath )) {
				@unlink ( $filepath );
			}
			
			// WebM
			$result = db_fetch_object ( $query );
			$filepath = ULICMS_ROOT . "/content/videos/" . basename ( $result->webm_file );
			if (! empty ( $result->webm_file ) and is_file ( $filepath )) {
				@unlink ( $filepath );
			}
			
			// MP4
			$filepath = ULICMS_ROOT . "/content/videos/" . basename ( $result->mp4_file );
			if (! empty ( $result->mp4_file ) and is_file ( $filepath )) {
				@unlink ( $filepath );
			}
			
			db_query ( "DELETE FROM " . tbname ( "videos" ) . " where id = " . intval ( $_REQUEST ["delete"] ) );
		}
		Request::redirect ( ModuleHelper::buildActionURL ( "videos" ) );
	}
}