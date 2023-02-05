<?php

declare(strict_types=1);

namespace UliCMS\Constants;

// comments have three states
// if a comment is pending
// it has to be approved by a backend user
class CommentStatus {

    const PENDING = "pending";
    const PUBLISHED = "published";
    const SPAM = "spam";
    const DEFAULT_STATUS = self::PENDING;

}
