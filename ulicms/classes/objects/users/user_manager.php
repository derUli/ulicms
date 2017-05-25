<?php
class UserManager {
	public function getUsersByGroupId($gid) {
		$users = array ();
		$sql = "select id from {prefix}users where `group_id` = ?";
		$args = array (
				intval ( $gid ) 
		);
		$query = Database::pQuery ( $sql, $args, true );
		while ( $row = Database::fetchObject ( $query ) ) {
			$users [] = new User ( $row->id );
		}
		return $users;
	}
	public function getLockedUsers($locked = true) {
		$users = array ();
		$sql = "select id from {prefix}users where `locked` = ?";
		$args = array (
				intval ( $locked ) 
		);
		$query = Database::pQuery ( $sql, $args, true );
		while ( $row = Database::fetchObject ( $query ) ) {
			$users [] = new User ( $row->id );
		}
		return $users;
	}
}