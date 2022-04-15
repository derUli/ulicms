<?php

namespace UliCMS\Exceptions;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Exceptions\DatabaseException;

class SqlException extends DatabaseException {
    
}
