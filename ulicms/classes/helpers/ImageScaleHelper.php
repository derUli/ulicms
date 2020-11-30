<?php

declare(strict_types=1);

namespace UliCMS\Helpers;

use Helper;
use Settings;
use ImagineHelper;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class ImageScaleHelper extends Helper
{
    public static function getMaxImageDimensions(): ?array
    {
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
