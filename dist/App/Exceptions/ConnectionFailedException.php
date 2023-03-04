<?php

namespace App\Exceptions;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use App\Exceptions\DatabaseException;

class ConnectionFailedException extends DatabaseException
{
}
