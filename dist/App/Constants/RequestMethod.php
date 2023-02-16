<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Use this class to compare HTTP request methods
 */
class RequestMethod
{
    public const POST = 'post';
    public const GET = 'get';
    public const HEAD = 'head';
    public const PUT = 'put';
    public const DELETE = 'delete';
    public const CONNECT = 'connect';
    public const OPTIONS = 'option';
    public const TRACE = 'trace';
}
