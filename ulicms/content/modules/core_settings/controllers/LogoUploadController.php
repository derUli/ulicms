<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class LogoUploadController extends Controller {

    public function _buildFileName(
            string $filename,
            string $originalName
    ): string {
        $extension = file_extension($originalName);
        $hash = md5_file($filename);
        return $hash . "." . $extension;
    }

    public function _buildFilePath(
            string $filename,
            string $originalName
    ): string {
        return ULICMS_DATA_STORAGE_ROOT . "/content/images/" .
                $this->_buildFileName($filename, $originalName);
    }

    public function upload(): void {
        // Logo Upload
        if (!empty($_FILES['logo_upload_file']['name'])) {
            $logo_upload = $_FILES['logo_upload_file'];
            $type = $logo_upload['type'];

            if ($type == "image/jpeg" or $type == "image/jpg"
                    or $type == "image/gif" or $type == "image/png") {
                $originalName = $logo_upload['name'];
                $newPath = $this->_buildFilePath(
                        $logo_upload['tmp_name'],
                        $originalName
                );

                do_event("before_upload_logo");
                move_uploaded_file($logo_upload['tmp_name'], $newPath);
                // Google Cloud: make file public
                if (startsWith(ULICMS_DATA_STORAGE_ROOT, "gs://")
                        and class_exists("GoogleCloudHelper")) {
                    GoogleCloudHelper::changeFileVisiblity($newPath, true);
                }

                Settings::set("logo_image", basename($newPath));
                Settings::set("logo_disabled", "no");

                CacheUtil::clearPageCache();
                do_event("after_upload_logo_successful");
            }

            do_event("after_upload_logo");
        }


        Request::redirect(ModuleHelper::buildActionURL("logo_upload"));
    }

    public function _deleteLogo(): bool {
        $logoImage = Settings::get("logo_image");
        $path = ULICMS_DATA_STORAGE_ROOT . "/content/images/${logoImage}";

        if (empty($logoImage) || !file_exists($path)) {
            return false;
        }

        @unlink($path);

        Settings::set("logo_image", "");
        Settings::set("logo_disabled", "yes");
        return true;
    }

    public function deleteLogo(): void {
        $success = $this->_deleteLogo();
        Response::sendHttpStatusCodeResultIfAjax(
                $success ?
                        HttpStatusCode::OK :
                        HttpStatusCode::INTERNAL_SERVER_ERROR,
                ModuleHelper::buildActionURL("logo")
        );
    }

}
