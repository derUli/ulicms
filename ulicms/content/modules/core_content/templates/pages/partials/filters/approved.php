<?php

use UliCMS\HTML\Input;

$controller = ControllerRegistry::get("PageController");
?>

<?php translate("approved"); ?>
<?php

echo Input::singleSelect("filter_approved", null, $controller->_getBooleanSelection());
