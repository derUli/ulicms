<?php

declare(strict_types=1);

namespace App\Constants;

// use this constants if you have to compare request methods
class RequestMethod
{
    const POST = "post";
    const GET = "get";
    const HEAD = "head";
    const PUT = "put";
    const DELETE = "delete";
    const CONNECT = "connect";
    const OPTIONS = "option";
    const TRACE = "trace";
}
