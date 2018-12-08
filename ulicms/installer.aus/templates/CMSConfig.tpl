<?php

class CMSConfig extends BaseConfig
{

    public $db_server = "{mysql_host}";

    public $db_user = "{mysql_user}";

    public $db_password = "{mysql_password}";

    public $db_database = "{mysql_database}";

    public $db_prefix = "{prefix}";

    public $db_type = "mysql";

    public $debug = false;

    public $log_requests = false;
}