<?php

declare(strict_types=1);

namespace App\Constants;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Use this class to compare HTTP request methods
 */
abstract class RequestMethod {
    public const POST = 'post';

    public const GET = 'get';

    public const HEAD = 'head';

    public const PUT = 'put';

    public const DELETE = 'delete';

    public const CONNECT = 'connect';

    public const OPTIONS = 'option';

    public const TRACE = 'trace';
}
