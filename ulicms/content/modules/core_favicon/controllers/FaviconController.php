<?php
class FaviconController extends Controller {
	public function doUpload() {
		// Favicon Upload
		if (! empty ( $_FILES ['favicon_upload_file'] ['name'] ) and $acl->hasPermission ( "favicon" )) {
			if (! file_exists ( "../content/images" )) {
				@mkdir ( "../content/images" );
				@chmod ( "../content/images", 0777 );
			}

			$favicon_upload_file = $_FILES ['favicon_upload_file'];
			$type = $favicon_upload_file ['type'];
			$filename = $favicon_upload_file ['name'];
			$extension = file_extension ( $filename );

			if (startsWith ( $type, "image/" )) {

				$new_filename = "../content/images/favicon.ico";

				add_hook ( "before_upload_favicon" );

				// move_uploaded_file ( $favicon_upload_file ['tmp_name'], $new_filename );
				require_once ULICMS_ROOT . '/classes/3rdparty/class-php-ico.php';
				$source = $favicon_upload_file ['tmp_name'];
				$destination = $new_filename;

				$sizes = array (
						array (
								32,
								32
						),
						array (
								64,
								64
						)
				);
				if (isset ( $_POST ["high_resolution"] )) {
					$sizes = array (
							array (
									32,
									32
							),
							array (
									64,
									64
							),
							array (
									128,
									128
							)
					);
				}
				$ico_lib = new PHP_ICO ( $source, $sizes );
				$ico_lib->save_ico ( $destination );

				add_hook ( "after_upload_favicon" );
				Request::redirect ( ModuleHelper::buildActionURL ( "favicon" ) );
			} else {
				Request::redirect ( ModuleHelper::buildActionURL ( "favicon", "error=UPLOAD_WRONG_FILE_FORMAT" ) );
			}
		}
	}
}
