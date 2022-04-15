<?php

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\HTML\Input;

$controller = ControllerRegistry::get("PageController");
?>

<?php translate("menu"); ?>
<?php

echo Input::singleSelect("filter_menu", null, $controller->_getMenuSelection());
