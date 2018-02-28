<?php
class GoogleCloudHelper {
	/**
	 * Are we running on the App Engine production environment?
	 *
	 * @return bool
	 */
	public static function isProduction() {
		return isset ( $_SERVER ['SERVER_SOFTWARE'] ) && strpos ( $_SERVER ['SERVER_SOFTWARE'], 'Google App Engine' ) !== false;
	}
	/**
	 * Are we running on the App Engine local development environment?
	 *
	 * @return bool
	 */
	public static function isDevelopment() {
		return isset ( $_SERVER ['SERVER_SOFTWARE'] ) && strpos ( $_SERVER ['SERVER_SOFTWARE'], 'Development/' ) === 0;
	}
	public static function writeCloudFile($filename, $content, $acl = "public-read") {
		$options = [ 
				'gs' => [ 
						'acl' => $acl 
				] 
		];
		$context = stream_context_create ( $options );
		file_put_contents ( $filename, $content, 0, $context );
	}
	public static function changeFileVisiblity($filename, $public) {
		if (! file_exists ( $filename )) {
			return false;
		}
		$content = file_get_contents ( $filename );
		if (! $content) {
			return false;
		}
		self::writeCloudFile ( $filename, $content, $public ? "public-read" : "private" );
		return true;
	}
	// php files should not be served from Google Cloud Storage
	public static function getProtectedFileExtensions() {
		$extensions = array (
				".php",
				".phps" 
		);
		$extensions = apply_filter ( $extensions, "protected_extensions" );
		return $extensions;
	}
	// Make all files public except forbidden extensions
	public static function makeFilesPublic($folder) {
		$extensions = self::getProtectedFileExtensions ();
		$files = find_all_files ( $folder );
		foreach ( $files as $file ) {
			self::changeFileVisiblity ( $file, ! endsWith ( strtolower ( $file ), ".php" ) );
		}
	}
}