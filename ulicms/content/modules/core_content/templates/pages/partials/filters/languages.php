<?php

use App\HTML\Input;

$controller = ControllerRegistry::get("PageController");
?>

<?php translate("language"); ?>
<?php

echo Input::singleSelect("filter_language", null, $controller->_getLanguageSelection());
