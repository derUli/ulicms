<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\HTML\Input;

$controller = ControllerRegistry::get('PageController');
?>

<?php translate('type'); ?>
<?php

echo Input::singleSelect('filter_type', null, $controller->_getTypeSelection());
