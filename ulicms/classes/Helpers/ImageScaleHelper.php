<?php

declare(strict_types=1);

namespace UliCMS\Helpers;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use Helper;
use Settings;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use UliCMS\Helpers\ImageScaleHelper;

/**
 * Utils for scaling images
 */
class ImageScaleHelper extends Helper {

    /**
     * Get maximum image dimensions from config
     * @return array|null Array of [width, height] or null if not configured
     */
    public static function getMaxImageDimensions(): ?array {
        $dimensions = null;
        $scale = strtolower(Settings::get("max_image_dimensions") ?? '');

        $explodedScale = explode("x", $scale);

        if (count($explodedScale) === 2) {
            if (!empty($explodedScale[0])) {
                $width = intval($explodedScale[0]);
            }
            if (!empty($explodedScale[1])) {
                $height = intval($explodedScale[1]);
            }
        }

        if ($width && $height) {
            $dimensions = [$width, $height];
        }

        return $dimensions;
    }

    /**
     * Scale Down an image to make it fit in max_image_dimensions
     * @param string $file Image File
     * @param string|null $outputFile Output File Name, if null it will overwrite the original file
     * @return bool
     */
    public static function scaleDown(
            string $file,
            ?string $outputFile = null
    ): bool {
        $dimensions = ImageScaleHelper::getMaxImageDimensions();

        $scaled = false;

        if ($dimensions) {
            $imagine = ImagineHelper::getImagine();

            $size = new Box($dimensions[0], $dimensions[1]);
            $mode = ImageInterface::THUMBNAIL_INSET;

            $imagine->open($file)
                    ->thumbnail($size, $mode)
                    ->save($outputFile ? $outputFile : $file);
            $scaled = true;
        }
        return $scaled;
    }

}
