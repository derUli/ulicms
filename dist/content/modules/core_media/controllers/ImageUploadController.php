<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Helpers\ImageScaleHelper;
use App\Security\Hash;
use App\Utils\File;
use App\Utils\Path;

/**
 * Image Upload Handler for CKEditor 5
 */
class ImageUploadController extends \App\Controllers\Controller {
    /**
     * Handle image uploads from CKEditor5
     * Files are uploaded by SimpeUpload plugin
     *
     * @return void
     */
    public function uploadPost(): void {
        $upload = $_FILES['upload'] ?? null;

        if(! $upload) {
            $response = [
                'error' => get_translation('error_no_file_uploaded')
            ];

            JSONResult($response);
        }

        $tmpPath = $upload['tmp_name'];
        $originalFilename = $upload['name'];
        $extension = File::getExtension($originalFilename);

        $fileContent = file_get_contents($tmpPath);

        $hash = Hash::hashCacheIdentifier($fileContent);
        $targetFilename = "{$hash}.{$extension}";

        $targetPath = Path::Resolve("ULICMS_CONTENT/images/{$targetFilename}");
        $url = "/content/images/{$targetFilename}";

        $scaled = ImageScaleHelper::scaleDown($tmpPath, $targetPath);

        if($scaled) {
            $response = [
                'url' => $url
            ];

            JSONResult($response);
        }

        $response = [
            'error' => get_translation('error_scaling_failed')
        ];
            
        JSONResult($response);

    }
}
