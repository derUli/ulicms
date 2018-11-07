<?php

class UsageStatisticsController extends MainClass
{

    public function beforeInit()
    {
        if ($_REQUEST["sClass"] = "UsageStatisticsController") {
            no_anti_csrf();
        }
    }

    public function submitStatistics()
    {
        $installation_id = Request::getVar("installation_id");
        if (StringHelper::isNullOrWhitespace($installation_id)) {
            HTTPStatusCodeResult(HttpStatusCode::BAD_REQUEST);
        }
        
        $php_version = Request::getVar("php_version");
        $mysql_version = Request::getVar("mysql_version");
        $ulicms_version = Request::getVar("ulicms_version");
        
        $installed_modules = Request::getVar("installed_modules[]");
        $installed_themes = Request::getVar("installed_themes[]");
        
        $php_extensions = Request::getVar("php_extensions[]");
        
        // Database::pQuery("insert into {prefix}usage_statistics
        // (installation_id, php_version, mysql_version, installed_modules,
        // installed_themes, php_extensions)", array(), true);
    }
}