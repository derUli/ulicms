<?php

declare(strict_types=1);

use Imagine\Image\AbstractImagine;
use Imagine\Exception\RuntimeException;

class ImagineHelper extends Helper {

    protected static function getImageMagick(): ?AbstractImagine {
        try {
            return new Imagine\Imagick\Imagine();
        } catch (RuntimeException $e) {
            return null;
        }
    }

    protected static function getGraphicsMagicks(): ?AbstractImagine {
        try {
            return new Imagine\Gmagick\Imagine();
        } catch (RuntimeException $e) {
            return null;
        }
    }

    protected static function getGD(): ?AbstractImagine {
        try {
            return new Imagine\Gd\Imagine();
        } catch (RuntimeException $e) {
            return null;
        }
    }

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

    public static function isSVGSupportAvailable() {
        $imagine = self::getImagine();
        return !($imagine instanceof Imagine\Gd\Imagine);
    }

}
