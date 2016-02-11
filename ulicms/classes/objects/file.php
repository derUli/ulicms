<?php
class File {
	public static function write($file, $data) {
		return file_put_contents ( $file, $data );
	}
	public static function append($file, $data) {
		return file_put_contents ( $file, $data, FILE_APPEND | LOCK_EX );
	}
	public static function read($file) {
		return file_get_contents ( $file );
	}
	public static function delete($file) {
		return unlink ( $file );
	}
	public static function rename($old, $new) {
		return rename ( $old, $new );
	}
	public static function lastChanged($file) {
		echo self::getLastChanged ( $file );
	}
	public static function getLastChanged($file) {
		$retval = null;
		clearstatcache ();
		$retval = filemtime ( $file );
		clearstatcache ();
		return $retval;
	}
	public static function getExtension($filename) {
		$ext = explode ( ".", $filename );
		$ext = end ( $ext );
		return $ext;
	}
}
