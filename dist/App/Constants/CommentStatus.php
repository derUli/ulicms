<?php

declare(strict_types=1);

namespace App\Constants;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

/**
 * Comments have three states
 * if a comment is pending
 * it has to be approved by a backend user
 */
class CommentStatus
{
    public const PENDING = 'pending';

    public const PUBLISHED = 'published';

    public const SPAM = 'spam';

    public const DEFAULT_STATUS = self::PENDING;
}
