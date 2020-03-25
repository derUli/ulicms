<?php 
use UliCMS\HTML\Input;
$controller = ControllerRegistry::get("PageController");
?>

<?php translate("type"); ?>
<?php
echo Input::singleSelect("filter_type", null, $controller->getTypeSelection());