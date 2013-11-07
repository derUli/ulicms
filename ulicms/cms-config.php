<?php
class config extends baseConfig{
    
    var $db_server = "localhost";
    var $db_user = "root";
    var $db_password = "password";
    var $db_database = "test";
    var $db_prefix = "ulicms_";
    var $db_type = "mysql";
    
    }
define("ULICMS_DEBUG", true);