<?php

include_once dirname(__FILE__)."/icons.php";
$controller = new InfoController();

echo $controller->_getComposerLegalInfo();
