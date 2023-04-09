<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use Settings;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

/**
 * Utils to scale down image uploads
 */
class ImageScaleHelper extends Helper
{
    /**
     * Get maximum allowed size dimension for images
     * @return array|null
     */
    public static function getMaxImageDimensions(): ?array
    {
        $dimensions = null;
        $scale = strtolower(Settings::get('max_image_dimensions') ?? '');

        $explodedScale = explode('x', $scale);

        if (count($explodedScale) === 2) {
            if (! empty($explodedScale[0])) {
                $width = (int) $explodedScale[0];
            }

            if (! empty($explodedScale[1])) {
                $height = (int) $explodedScale[1];
            }
        }

        if ($width && $height) {
            $dimensions = [$width, $height];
        }

        return $dimensions;
    }

    /**
     * Scale down huge images to make them fit max_image_dimensions
     * @param string $file
     * @param string|null $outputFile
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
