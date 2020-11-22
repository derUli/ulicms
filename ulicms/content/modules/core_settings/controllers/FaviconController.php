<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class FaviconController extends Controller {

    public function _getSizes($highResolution = false): array {
        $sizes = [
            [
                32,
                32
            ],
            [
                64,
                64
            ]
        ];

        if ($highResolution) {
            $sizes[] = [
                128,
                128
            ];
        }
        return $sizes;
    }

    public function _getDestination1(): string {
        return ULICMS_DATA_STORAGE_ROOT
                . "/content/images/favicon.ico";
    }

    public function _getDestination2(): string {
        return ULICMS_DATA_STORAGE_ROOT
                . "/favicon.ico";
    }

    public function doUpload(): void {
        // Favicon Upload
        if (!empty($_FILES['favicon_upload_file']['name'])) {
            if (!is_dir("../content/images")) {
                @mkdir("../content/images");
                @chmod("../content/images", 0777);
            }
            $favicon_upload_file = $_FILES['favicon_upload_file'];
            $type = $favicon_upload_file['type'];
            $filename = $favicon_upload_file['name'];
            $extension = file_extension($filename);

            if (startsWith($type, "image/")) {
                $destination1 = $this->_getDestination1();
                $destination2 = $this->_getDestination2();

                do_event("before_upload_favicon");

                $source = $favicon_upload_file['tmp_name'];

                $sizes = $this->_getSizes(isset($_POST["high_resolution"]));
                // TODO: extract to method _placeFiles($source, $sizes)
                $ico_lib = new PHP_ICO($source, $sizes);
                $ico_lib->save_ico($destination1);
                @$ico_lib->save_ico($destination2);

                // Google Cloud: make file public
                if (startsWith(ULICMS_DATA_STORAGE_ROOT, "gs://")
                        and class_exists("GoogleCloudHelper")) {
                    GoogleCloudHelper::changeFileVisiblity($destination1, true);
                }

                do_event("after_upload_favicon");

                CacheUtil::clearPageCache();

                Request::redirect(ModuleHelper::buildActionURL("favicon"));
            } else {
                Request::redirect(
                        ModuleHelper::buildActionURL(
                                "favicon",
                                "error=UPLOAD_WRONG_FILE_FORMAT"
                        )
                );
            }
        }
    }

    public function _deleteIcon(): bool {
        $success = true;

        $files = [
            $this->_getDestination1(),
            $this->_getDestination2()
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }

            if (file_exists($file)) {
                $success = false;
            }
        }
        return $success;
    }

}
