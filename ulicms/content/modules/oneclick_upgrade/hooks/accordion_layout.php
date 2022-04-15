<?php

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

echo Template::executeModuleTemplate("oneclick_upgrade", "Dashboard");
