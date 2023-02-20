<?php

namespace App\Exceptions;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use BadMethodCallException;

class NotImplementedException extends BadMethodCallException
{
}
