<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use Imagine\Image\AbstractImagine;
use Imagine\Exception\RuntimeException;

/**
 * We use Imagine for imagine manipulations
 */
class ImagineHelper extends Helper {

    const ACCEPT_MIMES = 'image/jpeg,image/png,image/gif';
    const GRAPHICS_MAGICK = 'gmagick';
    const IMAGE_MAGICK = 'imagick';
    const GD = 'imagick';

    /**
     * Get new ImageMagick Imagine adapter
     * @return AbstractImagine|null
     */
    protected static function getImageMagick(): ?AbstractImagine {
        try {
            return new Imagine\Imagick\Imagine();
        } catch (RuntimeException $e) {
            return null;
        }
    }

    /**
     * Get new Gmagick Imagine adapter
     * @return AbstractImagine|null
     */
    protected static function getGraphicsMagicks(): ?AbstractImagine {
        try {
            return new Imagine\Gmagick\Imagine();
        } catch (RuntimeException $e) {
            return null;
        }
    }

    /**
     * Get new GD Imagine adapter
     * @return AbstractImagine|null
     */
    protected static function getGD(): ?AbstractImagine {
        try {
            return new Imagine\Gd\Imagine();
        } catch (RuntimeException $e) {
            return null;
        }
    }

    /**
     * Get Image Adapter with first available adapter
     * @return AbstractImagine|null
     */
    public static function getImagine(): ?AbstractImagine {
        $imagineInterface = null;
        $libraries = self::getLibraries();

        foreach ($libraries as $imagine) {
            if ($imagine) {
                $imagineInterface = $imagine;
                break;
            }
        }

        return $imagineInterface;
    }

    /**
     * Get name of first available adapter
     * @return string|null
     */
    public static function getLibraryName(): ?string {
        $libraryName = null;
        $libraries = self::getLibraries();

        foreach ($libraries as $name => $imagine) {
            if ($imagine) {
                $libraryName = $name;
                break;
            }
        }

        return $libraryName;
    }

    public static function getLibraries(): array {
        return [
            self::GRAPHICS_MAGICK => self::getGraphicsMagicks(),
            self::IMAGE_MAGICK => self::getImageMagick(),
            self::GD => self::getGD()
        ];
    }

}
