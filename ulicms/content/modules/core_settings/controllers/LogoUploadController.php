<?php

class LogoUploadController extends Controller
{

    public function upload()
    {
        // Logo Upload
        if (! empty($_FILES['logo_upload_file']['name'])) {
            $logo_upload = $_FILES['logo_upload_file'];
            $type = $logo_upload['type'];
            $filename = $logo_upload['name'];
            $extension = file_extension($filename);
            
            if ($type == "image/jpeg" or $type == "image/jpg" or $type == "image/gif" or $type == "image/png") {
                $hash = md5(file_get_contents($logo_upload['tmp_name']));
                $new_filename = ULICMS_DATA_STORAGE_ROOT . "/content/images/" . $hash . "." . $extension;
                $logo_upload_filename = $hash . "." . $extension;
                
                do_event("before_upload_logo");
                move_uploaded_file($logo_upload['tmp_name'], $new_filename);
                // Google Cloud: make file public
                if (startsWith(ULICMS_DATA_STORAGE_ROOT, "gs://") and class_exists("GoogleCloudHelper")) {
                    GoogleCloudHelper::changeFileVisiblity($new_filename, true);
                }
                
                Settings::set("logo_image", $logo_upload_filename);
                do_event("after_upload_logo_successfull");
                Request::redirect(ModuleHelper::buildActionURL("logo_upload"));
            }
            
            do_event("after_upload_logo");
        }
        
        Settings::set("logo_disabled", Request::getVar("logo_disabled", "no", "str"));
        Request::redirect(ModuleHelper::buildActionURL("logo_upload"));
    }
}