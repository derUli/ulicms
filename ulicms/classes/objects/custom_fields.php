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
		$sql = "Select count(id) as amount, id from {prefix}custom_fields where content_id = ? and name = ?";
		$query = Database::pQuery ( $sql, $args, true );
		$result = Database::fetchObject ( $query );
		if ($result->amount > 0) {
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
			$sql = "INSERT INTO {prefix}custom_fields (content_id, name, value) (?, ?, ?)";
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