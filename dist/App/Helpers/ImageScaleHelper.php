<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Settings;

/**
 * Utils to scale down image uploads
 */
abstract class ImageScaleHelper extends Helper {
    /**
     * Get maximum allowed size dimension for images
     * @return int[]
     */
    public static function getMaxImageDimensions(): array {
        $dimensions = null;
        $scale = strtolower(Settings::get('max_image_dimensions') ?? '');

        $explodedScale = explode('x', $scale);

        $width = PHP_INT_MAX;
        $height = PHP_INT_MAX;

        if (count($explodedScale) === 2) {
            if (! empty($explodedScale[0])) {
                $width = (int)$explodedScale[0];
            }

            if (! empty($explodedScale[1])) {
                $height = (int)$explodedScale[1];
            }
        }

        $dimensions = array_values(apply_filter([$width, $height], 'image_max_dimensions'));

        return $dimensions;
    }


    /**
     * Scale down huge images to make them fit max_image_dimensions
     *
     * @param string $file
     * @param string|null $outputFile
     * @param array<number>|null $dimensions
     *
     * @return bool
     */
    public static function scaleDown(
        string $file,
        ?string $outputFile = null,
        ?array $dimensions = null

    ): bool {
        $dimensions = $dimensions ?? ImageScaleHelper::getMaxImageDimensions();

        $scaled = false;

        if ($dimensions) {
            $imagine = ImagineHelper::getImagine();

            $size = new Box($dimensions[0], $dimensions[1]);
            $mode = ImageInterface::THUMBNAIL_INSET;

            $imagine?->open($file)
                ->thumbnail($size, $mode)
                ->save($outputFile ?: $file);

            $scaled = true;
        }

        return $scaled;
    }
}
