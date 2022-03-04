<?php

declare(strict_types=1);

use Imagine\Image\AbstractImagine;
use Imagine\Exception\RuntimeException;
use UliCMS\Exceptions\NotSupportedException;

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
        $imagine = self::getImageMagick();

        if (!$imagine) {
            $imagine = self::getGraphicsMagicks();
        }

        if (!$imagine) {
            $imagine = self::getGD();
        }

        if (!$imagine) {
            throw new NotSupportedException(
                            "No graphics library installed\n" .
                            "Please install GD, ImageMagick or GraphicsMagick for PHP"
            );
        }

        return $imagine;
    }

    /**
     * Get name of first available adapter
     * @return string|null
     */
    public static function getLibraryName(): ?string {
        if (self::getImageMagick()) {
            return self::IMAGE_MAGICK;
        }
        if (self::getGraphicsMagicks()) {
            return self::GRAPHICS_MAGICK;
        }

        if (self::getGD()) {
            return self::GD;
        }
        return null;
    }

}
