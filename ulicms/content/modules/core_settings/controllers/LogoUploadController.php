<?php
class LogoUploadController extends Controller {
	public function upload() {
		// Logo Upload
		if (! empty ( $_FILES ['logo_upload_file'] ['name'] )) {
			if (! file_exists ( "../content/images" )) {
				@mkdir ( "../content/images" );
				@chmod ( "../content/images", 0777 );
			}
			
			$logo_upload = $_FILES ['logo_upload_file'];
			$type = $logo_upload ['type'];
			$filename = $logo_upload ['name'];
			$extension = file_extension ( $filename );
			
			if ($type == "image/jpeg" or $type == "image/jpg" or $type == "image/gif" or $type == "image/png") {
				$hash = md5 ( file_get_contents ( $logo_upload ['tmp_name'] ) );
				$new_filename = "../content/images/" . $hash . "." . $extension;
				$logo_upload_filename = $hash . "." . $extension;
				
				add_hook ( "before_upload_logo" );
				move_uploaded_file ( $logo_upload ['tmp_name'], $new_filename );
				$image_size = getimagesize ( $new_filename );
				if ($image_size [0] <= 500 and $image_size [1] <= 100) {
					setconfig ( "logo_image", $logo_upload_filename );
					add_hook ( "after_upload_logo_successfull" );
					Request::redirect ( ModuleHelper::buildActionURL ( "logo_upload" ) );
				} else {
					add_hook ( "after_upload_logo_failed" );
					Request::redirect ( ModuleHelper::buildActionURL ( "logo_upload", "error=to_big" ) );
					exit ();
				}
			}
			
			add_hook ( "after_upload_logo" );
			Request::redirect ( ModuleHelper::buildActionURL ( "logo_upload" ) );
		}
	}
}