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
}