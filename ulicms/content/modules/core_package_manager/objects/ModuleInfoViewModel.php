<?php

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

class ModuleInfoViewModel {

    public $name;
    public $version;
    public $source;
    public $source_url;
    public $customPermissions = [];
    public $adminPermission;
    public $manufacturerName;
    public $manufacturerUrl;

}
