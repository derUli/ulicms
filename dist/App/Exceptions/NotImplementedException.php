<?php

namespace App\Exceptions;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use BadMethodCallException;

class NotImplementedException extends BadMethodCallException
{
}
