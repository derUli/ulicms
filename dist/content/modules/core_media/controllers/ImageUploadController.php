<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Helpers\ImageScaleHelper;
use App\Security\Hash;
use App\Utils\File;
use App\Utils\Path;
use Nette\Utils\FileSystem;

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
        $datePath = date('Y') . '/' . date('m') . '/' . date('d');

        $baseDir = Path::Resolve('ULICMS_CONTENT/images/' . $datePath);

        if(! is_dir($baseDir)) {
            FileSystem::createDir($baseDir);
        }

        $upload = $_FILES['upload'] ?? null;
        $urls = [];

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

        $dimension = ImageScaleHelper::getSrcSetDimensions();

        foreach($dimension as $width => $size) {

            $targetFilename = "{$hash}-{$width}.{$extension}";

            $targetPath = "{$baseDir}/{$targetFilename}";
            $url = "/content/images/{$datePath}/{$targetFilename}";

            $scaled = ImageScaleHelper::scaleDown($tmpPath, $targetPath);

            if(! $scaled) {
                $response = [
                    'error' => get_translation('error_scaling_failed')
                ];
                JSONResult($response);
            }

            if(! isset($urls['default'])) {
                $urls['default'] = $url;
            }

            $urls[$width] = $url;
        }

        $response = [
            'urls' => $urls,
        ];

        JSONResult($response);

    }
}
