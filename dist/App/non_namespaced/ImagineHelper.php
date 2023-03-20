<?php

declare(strict_types=1);

use Imagine\Image\AbstractImagine;

/**
 * Helper methods for Imagine
 * @since 2023.2 Use always GD
 */
class ImagineHelper extends Helper
{
    public const ACCEPT_MIMES = 'image/jpeg,image/png,image/gif';

    /**
     * Get Imagine instance
     * @return AbstractImagine|null
     */
    public static function getImagine(): ?AbstractImagine
    {
        return new Imagine\Gd\Imagine();
    }
}
