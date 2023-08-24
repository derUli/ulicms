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
        // Path scheme:
        // ULICMS_CONTENT/images/2023-08-18/3313298010-1920.jpeg
        $datePath = date('Y') . '/' . date('m') . '/' . date('d');

        $baseDir = Path::Resolve('ULICMS_CONTENT/images/' . $datePath);
        $baseUrl = "/content/images/{$datePath}";

        // Create directory
        if(! is_dir($baseDir)) {
            FileSystem::createDir($baseDir);
        }

        $upload = $_FILES['upload'] ?? null;
        $urls = [];

        // If called without file abort here
        if(! $upload) {
            $response = [
                'error' => [
                    'message' => get_translation('error_no_file_uploaded')
                ]
            ];

            JSONResult($response);
        }

        // Uploaded file path
        $tmpPath = $upload['tmp_name'];
        $originalFilename = $upload['name'];
        $extension = File::getExtension($originalFilename);

        $extension = apply_filter($extension, 'image_output_extension');

        // Calculate hash
        $fileContent = file_get_contents($tmpPath);
        $hash = Hash::hashCacheIdentifier((string)$fileContent);

        // Get dimensions for responsive images
        $dimensions = ImageScaleHelper::getSrcSetDimensions();

        foreach($dimensions as $width => $size) {

            // Target path and url
            $targetFilename = "{$hash}-{$width}.{$extension}";
            $targetPath = "{$baseDir}/{$targetFilename}";
            $url = "{$baseUrl}/{$targetFilename}";

            // Scale image
            $scaled = ImageScaleHelper::scaleDown($tmpPath, $targetPath, $size);

            // If scaling image failed return error
            if(! $scaled) {
                $response = [
                    'error' => [
                        'message' => get_translation('error_scaling_failed')
                    ]
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
