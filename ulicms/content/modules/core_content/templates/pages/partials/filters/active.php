<?php

use UliCMS\HTML\Input;

$controller = ControllerRegistry::get("PageController");
?>

<?php translate("enabled"); ?>
<?php

echo Input::singleSelect("filter_active", null, $controller->getBooleanSelection());
