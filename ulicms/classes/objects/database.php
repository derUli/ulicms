<?php
class Database {
	// Abstraktion für Ausführen von SQL Strings
	public static function query($query) {
		include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "logger.php";
		log_db_query ( $query );
		global $db_connection;
		return mysqli_query ( $db_connection, $query );
	}
	public static function pQuery($query, $args = array()) {
		$preparedQuery = "";
		$chars = mb_str_split ( $query );
		include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "logger.php";
		$i = 0;
		foreach ( $chars as $char ) {
			if ($char != "?") {
				$preparedQuery .= $char;
			} else {
				$value = $args [$i];
				if (is_float ( $value )) {
					$value = str_replace ( ",", ".", floatval ( $value ) );
				} else if (is_int ( $value )) {
					$value = intval ( $value );
				} else if (is_bool ( $value )) {
					$value = ( int ) $value;
				} else {
					$value = "'" . self::escapeValue ( $value ) . "'";
				}
				$preparedQuery .= $value;
				$i ++;
			}
		}
		log_db_query ( $preparedQuery );
		return Database::query ( $preparedQuery );
	}
	public static function getPDOConnectionString() {
		$retval = "mysql://";
		$cfg = new config ();
		$retval .= $cfg->db_user;
		if (! empty ( $cfg->db_password ))
			$retval .= ":" . $cfg->db_password;
		$retval .= "@" . $cfg->db_server;
		$retval .= "/" . $cfg->db_database;
		$retval .= "?charset=utf8";
		return $retval;
	}
	public static function getServerVersion() {
		global $db_connection;
		return mysqli_get_server_info ( $db_connection );
	}
	public static function getClientInfo() {
		global $db_connection;
		return mysqli_get_client_info ( $db_connection );
	}
	public static function dropTable($table, $prefix = true) {
		if ($prefix) {
			$table = tbname ( $table );
		}
		
		$table = self::escapeName ( $table );
		return self::query ( "DROP TABLE $table" );
	}
	public static function selectAVG($table, $column, $where = "", $prefix = true) {
		if ($prefix) {
			$table = tbname ( $table );
		}
		
		$table = self::escapeName ( $table );
		$column = self::escapeName ( $column );
		$sql = "select avg($column) from $table";
		if (! isNotNullOrEmpty ( $where )) {
			$sql .= " where $where";
		}
		$result = Database::query ( $sql );
		return $result;
	}
	public static function selectMin($table, $column, $where = "", $prefix = true) {
		if ($prefix) {
			$table = tbname ( $table );
		}
		
		$table = self::escapeName ( $table );
		$column = self::escapeName ( $column );
		$sql = "select min($column) from $table";
		if (! isNotNullOrEmpty ( $where )) {
			$sql .= " where $where";
		}
		$result = Database::query ( $sql );
		return $result;
	}
	public static function deleteFrom($table, $where = "", $prefix = true) {
		if ($prefix) {
			$table = tbname ( $table );
		}
		
		$table = self::escapeName ( $table );
		
		$sql = "DELETE FROM $table";
		if (! isNotNullOrEmpty ( $where )) {
			$sql .= " where $where";
		}
		$result = Database::query ( $sql );
		return $result;
	}
	public static function selectMax($table, $column, $where = "", $prefix = true) {
		if ($prefix) {
			$table = tbname ( $table );
		}
		
		$table = self::escapeName ( $table );
		$column = self::escapeName ( $column );
		$sql = "select min($column) from $table";
		if (! isNotNullOrEmpty ( $where )) {
			$sql .= " where $where";
		}
		$result = Database::query ( $sql );
		return $result;
	}
	public static function truncateTable($table, $prefix = true) {
		if ($prefix) {
			$table = tbname ( $table );
		}
		
		$table = self::escapeName ( $table );
		return self::query ( "TRUNCATE TABLE $table" );
	}
	public static function dropColumn($table, $column, $prefix = true) {
		if ($prefix) {
			$table = tbname ( $table );
		}
		
		$column = self::escapeName ( $column );
		$table = self::escapeName ( $table );
		return self::query ( "ALTER TABLE $table DROP COLUMN $table" );
	}
	
	// Using SQL Prepared statements
	public static function preparedQuery($sql, $typeDef = FALSE, $params = FALSE) {
		global $db_connection;
		if ($stmt = mysqli_prepare ( $db_connection, $sql )) {
			if (count ( $params ) == count ( $params, 1 )) {
				$params = array (
						$params 
				);
				$multiQuery = FALSE;
			} else {
				$multiQuery = TRUE;
			}
			
			if ($typeDef) {
				$bindParams = array ();
				$bindParamsReferences = array ();
				$bindParams = array_pad ( $bindParams, (count ( $params, 1 ) - count ( $params )) / count ( $params ), "" );
				foreach ( $bindParams as $key => $value ) {
					$bindParamsReferences [$key] = & $bindParams [$key];
				}
				array_unshift ( $bindParamsReferences, $typeDef );
				$bindParamsMethod = new ReflectionMethod ( 'mysqli_stmt', 'bind_param' );
				$bindParamsMethod->invokeArgs ( $stmt, $bindParamsReferences );
			}
			
			$result = array ();
			foreach ( $params as $queryKey => $query ) {
				foreach ( $bindParams as $paramKey => $value ) {
					$bindParams [$paramKey] = $query [$paramKey];
				}
				$queryResult = array ();
				if (mysqli_stmt_execute ( $stmt )) {
					$resultMetaData = mysqli_stmt_result_metadata ( $stmt );
					if ($resultMetaData) {
						$stmtRow = array ();
						$rowReferences = array ();
						while ( $field = mysqli_fetch_field ( $resultMetaData ) ) {
							$rowReferences [] = & $stmtRow [$field->name];
						}
						mysqli_free_result ( $resultMetaData );
						$bindResultMethod = new ReflectionMethod ( 'mysqli_stmt', 'bind_result' );
						$bindResultMethod->invokeArgs ( $stmt, $rowReferences );
						while ( mysqli_stmt_fetch ( $stmt ) ) {
							$row = array ();
							foreach ( $stmtRow as $key => $value ) {
								$row [$key] = $value;
							}
							$queryResult [] = $row;
						}
						mysqli_stmt_free_result ( $stmt );
					} else {
						$queryResult [] = mysqli_stmt_affected_rows ( $stmt );
					}
				} else {
					$queryResult [] = FALSE;
				}
				$result [$queryKey] = $queryResult;
			}
			mysqli_stmt_close ( $stmt );
		} else {
			$result = FALSE;
		}
		
		if ($multiQuery) {
			return $result;
		} else {
			return $result [0];
		}
	}
	public static function escapeName($name) {
		$name = "`" . db_escape ( $name ) . "`";
		$name = str_replace ( "'", "", $name );
		$name = str_replace ( "\"", "", $name );
		return $name;
	}
	public static function getLastInsertID() {
		global $db_connection;
		return mysqli_insert_id ( $db_connection );
	}
	public static function getInsertID() {
		return self::getLastInsertID ();
	}
	
	// Fetch Row in diversen Datentypen
	public static function fetchArray($result) {
		return mysqli_fetch_array ( $result );
	}
	public static function fetchField($result) {
		return mysqli_fetch_field ( $result );
	}
	public static function fetchAssoc($result) {
		return mysqli_fetch_assoc ( $result );
	}
	public static function fetchAll($result, $resulttype = MYSQLI_NUM) {
		if (function_exists ( "mysqli_fetch_all" )) {
			return mysqli_fetch_all ( $result, $resulttype );
		}
		
		// @FIXME : $resulttype in alternativer Implementation von fetch_all behandeln
		$retval = array ();
		while ( $row = self::fetchAssoc ( $result ) ) {
			$retval [] = $row;
		}
		
		return $retval;
	}
	public static function close() {
		global $db_connection;
		mysqli_close ( $db_connection );
	}
	
	// Connect with database server
	public static function connect($server, $user, $password) {
		global $db_connection;
		$db_connection = mysqli_connect ( $server, $user, $password );
		if (! $db_connection)
			return false;
		self::query ( "SET NAMES 'utf8'" );
		// sql_mode auf leer setzen, da sich UliCMS nicht im strict_mode betreiben lässt
		self::query ( "SET SESSION sql_mode = '';" );
		
		return $db_connection;
	}
	// Datenbank auswählen
	public static function select($schema) {
		global $db_connection;
		return mysqli_select_db ( $db_connection, $schema );
	}
	public static function getNumFieldCount($result) {
		global $db_connection;
		return mysqli_field_count ( $db_connection );
	}
	public static function getAffectedRows() {
		global $db_connection;
		return mysqli_affected_rows ( $db_connection );
	}
	public static function fetchObject($result) {
		return mysqli_fetch_object ( $result );
	}
	public static function fetchRow($result) {
		return mysqli_fetch_row ( $result );
	}
	public static function getNumRows($result) {
		return mysqli_num_rows ( $result );
	}
	public static function getLastError() {
		global $db_connection;
		return mysqli_error ( $db_connection );
	}
	public static function error() {
		return self::getLastError ();
	}
	public static function getAllTables() {
		global $db_connection;
		$tableList = array ();
		$res = mysqli_query ( $db_connection, "SHOW TABLES" );
		while ( $cRow = mysqli_fetch_array ( $res ) ) {
			$tableList [] = $cRow [0];
		}
		
		sort ( $tableList );
		return $tableList;
	}
	
	// Abstraktion für Escapen von Werten
	public static function escapeValue($value, $type = null) {
		global $db_connection;
		if (is_null ( $type )) {
			
			if (is_float ( $value )) {
				return floatval ( $value );
			} else if (is_int ( $value )) {
				return intval ( $value );
			} else if (is_bool ( $value )) {
				return ( int ) $value;
			} else {
				return mysqli_real_escape_string ( $db_connection, $value );
			}
		} else {
			if ($type === DB_TYPE_INT) {
				return intval ( $value );
			} else if ($type === DB_TYPE_FLOAT) {
				return floatval ( $value );
			} else if ($type === DB_TYPE_STRING) {
				return mysqli_real_escape_string ( $db_connection, $value );
			} else if ($type === DB_TYPE_BOOL) {
				return intval ( $value );
			} else {
				return $value;
			}
		}
	}
	public static function getColumnNames($table) {
		$retval = array ();
		$table = tbname ( $table );
		$query = Database::query ( "SELECT * FROM $table limit 1" );
		$fields_num = self::getNumFieldCount ( $query );
		if ($fields_num > 0) {
			for($i = 0; $i < $fields_num; $i ++) {
				$field = db_fetch_field ( $query );
				$retval [] = $field->name;
			}
			sort ( $retval );
		}
		
		return $retval;
	}
}

// Alias für Database
class DB extends Database {
}
