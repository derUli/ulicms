<?php
if (! defined ( "KCFINDER_PAGE" )) {
	class Path {
		public static function resolve($path) {
			$path = str_ireplace ( "ULICMS_ROOT", ULICMS_ROOT, $path );
			$path = str_ireplace ( "ULICMS_TMP", ULICMS_TMP, $path );
			$path = str_ireplace ( "ULICMS_CACHE", ULICMS_CACHE, $path );
			$path = str_ireplace ( "\\", "/", $path );
			$path = rtrim ( $path, "/" );
			return $path;
		}
		public static function removeDir($dir, $DeleteMe) {
			if (! $dh = @opendir ( $dir ))
				return;
			while ( false !== ($obj = readdir ( $dh )) ) {
				if ($obj == '.' || $obj == '..')
					continue;
				if (! @unlink ( $dir . '/' . $obj ))
					self::removeDir ( $dir . '/' . $obj, true );
			}
			
			closedir ( $dh );
			if ($DeleteMe) {
				@rmdir ( $dir );
			}
		}
	}
}