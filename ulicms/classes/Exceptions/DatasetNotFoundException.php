<?php

namespace UliCMS\Exceptions;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use Exception;

class DatasetNotFoundException extends SqlException {
    
}