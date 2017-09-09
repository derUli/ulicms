<?php
class HomeController extends Controller {
	public function getModel() {
		$model = new HomeViewModel ();
		$query = Database::query ( "SELECT count(id) as amount FROM `{prefix}content`", true );
		$result = Database::fetchObject ( $query );
		$model->contentCount = $result->amount;
		
		$topPages = Database::query ( "SELECT language, systemname, title, `views` FROM " . tbname ( "content" ) . " WHERE redirection NOT LIKE '#%' ORDER BY `views` DESC LIMIT 5", false );
		while ( $row = Database::fetchObject ( $topPages ) ) {
			$model->topPages[] = $row;
		}
		
		$lastModfiedPages = Database::query ( "SELECT language, systemname, title, lastmodified, case when lastchangeby is not null and lastchangeby > 0 then lastchangeby else autor end as lastchangeby FROM " . tbname ( "content" ) . " WHERE redirection NOT LIKE '#%' ORDER BY lastmodified DESC LIMIT 5", false );
		while ( $row = Database::fetchObject ( $lastModfiedPages ) ) {
			$model->lastModfiedPages [] = $row;
		}
		
		$admins_query = Database::query ( "SELECT id, username FROM " . tbname ( "users" ) );
		
		
		while ( $row = Database::fetchObject ( $admins_query ) ) {
			$admins [$row->id] = $row->username;
		}
		$model->admins = $admins;
		return $model;
	}
}