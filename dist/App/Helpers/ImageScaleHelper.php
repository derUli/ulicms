<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Utils\File;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\BoxInterface;
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

        return array_values(apply_filter([$width, $height], 'image_max_dimensions'));
    }

    /**
     * Calculate src set dimensions
     *
     * @param array<number>|null $dimensions
     *
     * @return array<int[]>
     */
    public static function getSrcSetDimensions(?array $dimensions = null): array {
        $srcSets = [];

        $iterations = 3;
        $dimensions = $dimensions ?? static::getMaxImageDimensions();

        $width = $dimensions[0];
        $height = $dimensions[1];

        for($i = 1; $i <= $iterations; $i++) {

            $srcSets[(int)$width] = [(int)$width, (int)$height];

            if(! isset($srcSets['default'])) {
                $srcSets['default'] = [(int)$width, (int)$height];
            }

            $width = $width / 2;
            $height = $height / 2;
        }

        return apply_filter($srcSets, 'image_srcset_dimensions');
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

            $size = new Box((int)$dimensions[0], (int)$dimensions[1]);
            $mode = ImageInterface::THUMBNAIL_INSET;

            $qualitySettings = static::getQualitySettings($file);

            $imagine->open($file)
                ->thumbnail($size, $mode)
                ->save($outputFile ?: $file, $qualitySettings);

            $scaled = true;
        }

        return $scaled;
    }


    public static function getImageSize(string $file): BoxInterface {
        $imagine = ImagineHelper::getImagine();
        $image = $imagine->open($file);        

        return $image->getSize();

    }

    /**
     * Get quality settings array for file
     *
     * @return array<string, int>
     */
    public static function getQualitySettings(string $file): array {
        $mimeType = File::getMime($file);

        switch($mimeType) {
            case 'image/jpeg':
                return [
                    'jpeg_quality' => (int)Settings::get('image_compression_jpeg')
                ];
            case 'image/png':
                return [
                    'png_compression_level' => (int)Settings::get('image_compression_png')
                ];
            case 'image/webp':
                return [
                    'webp_quality' => (int)Settings::get('image_compression_webp')
                ];
            default:
                return [];
        }

    }
}
