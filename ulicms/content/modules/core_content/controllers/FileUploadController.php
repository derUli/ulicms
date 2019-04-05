<?php

class FileUploadController extends Controller {

    public function uploadImage() {
        $result = array();

        $success = false;


        if (isset($_FILES["upload"])) {
            $file = $_FILES["upload"];
            $originalFilename = basename($file["name"]);
            $tempFileName = $file["tmp_name"];
            $type = get_mime($tempFileName);

            $uploadDir = apply_filter(Path::resolve("ULICMS_ROOT/content/images/ckeditor-uploads"), "ckeditor_upload_dir");

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, umask(), true);
            }

            // FIXME: Append timestamp to filename
            // to prevent accidentally overwriting files
            $fileNameWithTimestamp = time() . "-" . $originalFilename;
            $uploadFile = "$uploadDir/$fileNameWithTimestamp";

            $uploadUrl = ModuleHelper::getBaseUrl(apply_filter("/content/images/ckeditor-uploads/$fileNameWithTimestamp", "ckeditor_upload_url"));

            if (startsWith($type, "image") and move_uploaded_file($tempFileName, $uploadFile)) {
                $success = true;
            }
        } else {
            $result["error"] = get_translation("no_files_uploaded");
        }

        if ($success) {
            $result["uploaded"] = true;

            $result["url"] = $uploadUrl;
        } else {
            $result["uploaded"] = false;
            if (!isset($result["error"])) {
                $result["error"] = get_translation("upload_failed");
            }
        }
        JSONResult($result);
    }

}
