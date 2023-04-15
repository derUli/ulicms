<?php

namespace App\Exceptions;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use UnexpectedValueException;

class CorruptDownloadException extends UnexpectedValueException
{
}
