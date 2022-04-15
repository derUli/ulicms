<?php

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

class CMSConfig extends BaseConfig {

    public $db_server = "{mysql_host}";
    public $db_user = "{mysql_user}";
    public $db_password = "{mysql_password}";
    public $db_database = "{mysql_database}";
    public $db_prefix = "{prefix}";
    public $debug = false;
    public $exception_logging = true;
    public $log_requests = false;
    public $default_menu = "not_in_menu";
    public $default_content_type = "page";
    
}
