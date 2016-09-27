<?php
// Abstraktion für Ausführen von SQL Strings
function db_query($query) {
	return Database::query ( $query );
}
function getPDOConnectionString() {
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
function db_get_server_info() {
	return mysqli_get_server_info ( Database::getConnection () );
}
function db_get_client_info() {
	return mysqli_get_client_info ( Database::getConnection () );
}

// Using SQL Prepared statements
function db_prepared_query($sql, $typeDef = FALSE, $params = FALSE) {
	if ($stmt = mysqli_prepare ( Database::getConnection (), $sql )) {
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
function db_name_escape($name) {
	return "`" . db_escape ( $name ) . "`";
}
function db_last_insert_id() {
	return mysqli_insert_id ( Database::getConnection () );
}
function db_insert_id() {
	return db_last_insert_id ();
}

// Fetch Row in diversen Datentypen
function db_fetch_array($result) {
	return mysqli_fetch_array ( $result );
}
function db_fetch_field($result) {
	return mysqli_fetch_field ( $result );
}
function db_fetch_assoc($result) {
	return mysqli_fetch_assoc ( $result );
}
function db_fetch_all($result, $resulttype = MYSQLI_NUM) {
	if (function_exists ( "mysqli_fetch_all" ))
		return mysqli_fetch_all ( $result, $resulttype );
		
		// @FIXME : $resulttype in alternativer Implementation von fetch_all behandeln
	$retval = array ();
	while ( $row = db_fetch_assoc ( $result ) ) {
		$retval [] = $row;
	}
	
	return $retval;
}
function db_close() {
	mysqli_close ( Database::getConnection () );
}

// Connect with database server
function db_connect($server, $user, $password) {
	Database::setConnection ( mysqli_connect ( $server, $user, $password ) );
	if (! Database::getConnection ())
		return false;
	db_query ( "SET NAMES 'utf8'" );
	// sql_mode auf leer setzen, da sich UliCMS nicht im strict_mode betreiben lässt
	db_query ( "SET SESSION sql_mode = '';" );
	
	return Database::getConnection ();
}
// Datenbank auswählen
function db_select($schema) {
	return mysqli_select_db ( Database::getConnection (), $schema );
}
function db_num_fields($result) {
	return mysqli_field_count ( Database::getConnection () );
}
function db_affected_rows() {
	return mysqli_affected_rows ( Database::getConnection () );
}
function schema_select($schema) {
	return db_select ( $schema );
}
function db_select_db($schema) {
	return schema_select ( $schema );
}
function db_fetch_object($result) {
	return mysqli_fetch_object ( $result );
}
function db_fetch_row($result) {
	return mysqli_fetch_row ( $result );
}
function db_num_rows($result) {
	return mysqli_num_rows ( $result );
}
function db_last_error() {
	return mysqli_error ( Database::getConnection () );
}
function db_error() {
	return db_last_error ();
}
function db_get_tables() {
	$tableList = array ();
	$res = mysqli_query ( Database::getConnection (), "SHOW TABLES" );
	while ( $cRow = mysqli_fetch_array ( $res ) ) {
		$tableList [] = $cRow [0];
	}
	
	sort ( $tableList );
	return $tableList;
}
function db_real_escape_string($value) {
	return mysqli_real_escape_string ( Database::getConnection (), $value );
}

define ( "DB_TYPE_INT", 1 );
define ( "DB_TYPE_FLOAT", 2 );
define ( "DB_TYPE_STRING", 3 );
define ( "DB_TYPE_BOOL", 4 );

// Abstraktion für Escapen von Werten
function db_escape($value, $type = null) {
	if (is_null ( $type )) {
		
		if (is_float ( $value )) {
			return floatval ( $value );
		} else if (is_int ( $value )) {
			return intval ( $value );
		} else if (is_bool ( $value )) {
			return ( int ) $value;
		} else {
			return mysqli_real_escape_string ( Database::getConnection (), $value );
		}
	} else {
		if ($type === DB_TYPE_INT) {
			return intval ( $value );
		} else if ($type === DB_TYPE_FLOAT) {
			return floatval ( $value );
		} else if ($type === DB_TYPE_STRING) {
			return mysqli_real_escape_string ( Database::getConnection (), $value );
		} else if ($type === DB_TYPE_BOOL) {
			return intval ( $value );
		} else {
			return $value;
		}
	}
}

?>
