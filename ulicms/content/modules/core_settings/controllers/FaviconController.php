<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class FaviconController extends Controller
{
    public function _getSizes($highResolution = false): array
    {
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

    public function _getDestination1(): string
    {
        return ULICMS_DATA_STORAGE_ROOT
                . "/content/images/favicon.ico";
    }

    public function _getDestination2(): string
    {
        return ULICMS_DATA_STORAGE_ROOT
                . "/favicon.ico";
    }

    public function doUpload(): void
    {
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
                $this->_placeFiles($source, $sizes);

                // Google Cloud: make file public
                if (startsWith(ULICMS_DATA_STORAGE_ROOT, "gs://")
                        and class_exists("GoogleCloudHelper")) {
                    GoogleCloudHelper::changeFileVisiblity($destination1, true);
                    GoogleCloudHelper::changeFileVisiblity($destination2, true);
                }

                do_event("after_upload_favicon");

                CacheUtil::clearPageCache();

                Request::redirect(ModuleHelper::buildActionURL("favicon"));
            }

            // Show error if uploaded file is not an image
            Request::redirect(
                ModuleHelper::buildActionURL(
                    "favicon",
                    "error=UPLOAD_WRONG_FILE_FORMAT"
                )
            );
        }
    }

    public function _placeFiles(string $source, array $sizes): bool
    {
        $success = [];
        $files = [
            $this->_getDestination1(),
            $this->_getDestination2()
        ];

        $icoLib = new PHP_ICO($source, $sizes);

        foreach ($files as $file) {
            @$fileSaved = $icoLib->save_ico($file);

            $success[] = file_exists($file);
        }
        return count(array_filter($success)) > 0;
    }

    public function _deleteFavicon(): bool
    {
        $success = [];

        $files = [
            $this->_getDestination1(),
            $this->_getDestination2()
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }

            $success[] = !file_exists($file);
        }

        return count(array_filter($success)) > 0;
    }

    public function deleteFavicon(): void
    {
        $success = $this->_deleteFavicon();
        Response::sendHttpStatusCodeResultIfAjax(
            $success ?
                        HttpStatusCode::OK :
                        HttpStatusCode::INTERNAL_SERVER_ERROR,
            ModuleHelper::buildActionURL("favicon")
        );
    }

    public function _hasFavicon(): bool
    {
        return file_exists($this->_getDestination2());
    }
}
