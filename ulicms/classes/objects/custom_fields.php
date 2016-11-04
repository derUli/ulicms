<?php
class CustomFields {
	public static function set($name, $value, $content_id = null) {
		if (is_null ( $content_id )) {
			$content_id = get_ID ();
		}
		$content_id = intval ( $content_id );
		$args = array (
				$content_id,
				$name 
		);
		$sql = "Select id from {prefix}custom_fields where content_id = ? and name = ?";
		$query = Database::pQuery ( $sql, $args, true );
		if (Database::getNumRows($result) > 0) {
			$result = Database::fetchObject ( $query );
			if (is_null ( $value )) {
				$args = array (
						$result->id 
				);
				$sql = "DELETE FROM {prefix}custom_fields where id = ?";
				return Database::query ( $sql, $args, true );
			} else {
				$args = array (
						$value,
						$content_id 
				);
				$sql = "UPDATE {prefix}custom_fields set value = ? where content_id = ?";
				return Database::pQuery ( $sql, $args, true );
			}
		} else {
			$args = array (
					$content_id,
					$name,
					$value 
			);
			$sql = "INSERT INTO {prefix}custom_fields (content_id, name, value) VALUES(?, ?, ?)";
			return Database::pQuery ( $sql, $args, true );
		}
	}
	public static function get($name, $content_id = null) {
		if (is_null ( $content_id )) {
			$content_id = get_ID ();
		}
		$content_id = intval ( $content_id );
		$args = array (
				$content_id,
				$name 
		);
		$sql = "Select value from {prefix}custom_fields where content_id = ? and name = ?";
		$query = Database::pQuery ( $sql, $args, true );
		if (Database::getNumRows ( $query ) > 0) {
			$result = Database::fetchObject ( $query );
			return $result->value;
		} else {
			return null;
		}
	}
}