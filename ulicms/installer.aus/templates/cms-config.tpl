<?php
class config extends BaseConfig {
	var $db_server = "{mysql_host}";
	var $db_user = "{mysql_user}";
	var $db_password = "{mysql_password}";
	var $db_database = "{mysql_database}";
	var $db_prefix = "{prefix}";
	var $db_type = "mysql";
	var $debug = false;
	var $log_requests = false;
}