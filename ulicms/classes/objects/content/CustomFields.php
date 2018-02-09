<?php
class CustomFields {
	public static function set($name, $value, $content_id = null) {
		if (is_null ( $content_id )) {
			$content_id = get_ID ();
		}
		// use two nullbytes as seperator for arrays
		if (is_array ( $value )) {
			$value = join ( "\0\0", $value );
		}
		
		$content_id = intval ( $content_id );
		$args = array (
				$content_id,
				$name 
		);
		$sql = "Select id from {prefix}custom_fields where content_id = ? and name = ?";
		$query = Database::pQuery ( $sql, $args, true );
		if (Database::getNumRows ( $query ) > 0) {
			$result = Database::fetchObject ( $query );
			if (is_null ( $value )) {
				$args = array (
						intval ( $result->id ) 
				);
				$sql = "DELETE FROM {prefix}custom_fields where id = ?";
				return Database::pQuery ( $sql, $args, true );
			} else {
				$args = array (
						$value,
						$name,
						$content_id 
				);
				$sql = "UPDATE {prefix}custom_fields set value = ? where name = ? and content_id = ?";
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
			$value = $result->value;
			// if string contains double null bytes it is an array
			// FIXME: Use new boolean "array" Attribute
			if (str_contains ( "\0\0", $value )) {
				$value = explode ( "\0\0", $value );
			}
			return $value;
		} else {
			return null;
		}
	}
}