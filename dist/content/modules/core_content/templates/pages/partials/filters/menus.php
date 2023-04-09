<?php

use App\HTML\Input;

$controller = ControllerRegistry::get('PageController');
?>

<?php translate('menu'); ?>
<?php

echo Input::singleSelect('filter_menu', null, $controller->_getMenuSelection());
