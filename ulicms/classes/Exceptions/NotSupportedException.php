<?php

namespace UliCMS\Exceptions;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use InvalidArgumentException;

class NotSupportedException extends NotImplementedException {
    
}
