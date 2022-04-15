<?php

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\HTML\Input;

$controller = ControllerRegistry::get("PageController");
?>

<?php translate("language"); ?>
<?php

echo Input::singleSelect("filter_language", null, $controller->_getLanguageSelection());
