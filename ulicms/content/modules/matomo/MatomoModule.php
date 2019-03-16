<?php

class MatomoModule extends Controller {

    private $moduleName = "matomo";

    public function frontendFooter() {
        echo Template::executeModuleTemplate($this->moduleName, "matomo.php");
    }

    public function settings() {
        $acl = new ACL();
        if (Request::isPost() and $acl->hasPermission(getModuleMeta($this->moduleName, "admin_permission"))) {
            Settings::set("matomo_url", trim(Request::getVar("matomo_url")), "str");
            Settings::set("matomo_site_id", Request::getVar("matomo_site_id", null, "int"));
        }
        return Template::executeModuleTemplate($this->moduleName, "settings.php");
    }

    public function uninstall() {
        Settings::delete("matomo_url");
        Settings::delete("matomo_site_id");
    }

}
