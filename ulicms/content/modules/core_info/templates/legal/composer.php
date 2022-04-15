<?php

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

include_once dirname(__FILE__) . "/icons.php";
$controller = new InfoController();

echo $controller->_getComposerLegalInfo();
