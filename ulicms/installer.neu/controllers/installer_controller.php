<?php
class InstallerController {
	public static function getStep() {
		if (isset ( $_REQUEST ["step"] ) and ! empty ( $_REQUEST ["step"] )) {
			$step = intval ( $_REQUEST ["step"] );
		} else {
			$step = 1;
		}
		return $step;
	}
	public static function initSessionVars() {
		$vars = array (
				"mysql_user",
				"mysql_host",
				"mysql_password",
				"mysql_database",
				"language"
		);
		foreach ( $vars as $var ) {
			if (! isset ( $_SESSION [$var] )) {
				$_SESSION [$var] = "";
			}
		}
	}
	public static function loadLanguageFile($lang) {
		include_once "lang/" . $lang . ".php";
		include_once "lang/all.php";
	}
	public static function getLanguage() {
		if (isset ( $_SESSION ["language"] ) and ! empty ( $_SESSION ["language"] )) {
			return basename ( $_SESSION ["language"] );
		} else {
			$_SESSION ["language"] = "en";
			return "en";
		}
	}
	public static function getTitle() {
		return constant ( "TRANSLATION_TITLE_STEP_" . self::getStep () );
	}
	public static function getFooter() {
		$version = new ulicms_version ();
		return "&copy; 2011 - " . $version->getReleaseYear () . " by <a href=\"http://www.ulicms.de\" target=\"_blank\">UliCMS</a>";
	}
	public static function submitAdminData() {
		$_SESSION ["admin_password"] = $_POST ["admin_password"];
		$_SESSION ["admin_user"] = $_POST ["admin_user"];
		$_SESSION ["admin_email"] = $_POST ["admin_email"];
		$_SESSION ["admin_lastname"] = $_POST ["admin_lastname"];
		$_SESSION ["admin_firstname"] = $_POST ["admin_firstname"];
		header ( "Location: index.php?step=5" );
	}
	public static function submitTryConnect() {
		@$connection = mysqli_connect ( $_POST ["servername"], $_POST ["loginname"], $_POST ["passwort"] );
		if ($connection == false) {
			die ( TRANSLATION_DB_CONNECTION_FAILED );
		}

		// Check if database is present else try to create it.
		$query = mysqli_query ( $connection, "SHOW DATABASES" );
		$databases = array ();
		while ( $row = mysqli_fetch_array ( $query ) ) {
			$databases [] = $row [0];
		}

		if (! in_array ( $_POST ["datenbank"], $databases )) {
			// Try to create database if it not exists
			mysqli_query ( $connection, "CREATE DATABASE " . mysqli_real_escape_string ( $connection, $_POST ["datenbank"] ) );
		}

		@$select = mysqli_select_db ( $connection, $_POST ["datenbank"] );

		if ($select == false) {
			die ( TRANSLATION_CANT_OPEN_SCHEMA );
		}

		$_SESSION ["mysql_host"] = $_POST ["servername"];
		$_SESSION ["mysql_user"] = $_POST ["loginname"];
		$_SESSION ["mysql_password"] = $_POST ["passwort"];
		$_SESSION ["mysql_database"] = $_POST ["datenbank"];
	}
}
