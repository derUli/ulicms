<?php

declare(strict_types=1);

namespace App\Constants;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Bootstrap 3.x Button Styles
 */
abstract class ButtonType {
    public const BUTTON_BUTTON = 'button';

    public const BUTTON_SUBMIT = 'submit';

    public const BUTTON_RESET = 'reset';

    public const TYPE_BASIC = 'btn';

    public const TYPE_DEFAULT = 'btn btn-default';

    public const TYPE_PRIMARY = 'btn btn-primary';

    public const TYPE_SUCCESS = 'btn btn-success';

    public const TYPE_INFO = 'btn btn-info';

    public const TYPE_WARNING = 'btn btn-warning';

    public const TYPE_DANGER = 'btn btn-danger';

    public const TYPE_LINK = 'btn btn-link';
}
