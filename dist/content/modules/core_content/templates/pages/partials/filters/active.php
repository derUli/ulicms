<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\HTML\Input;

$controller = ControllerRegistry::get('PageController');
?>

<?php translate('enabled'); ?>
<?php

echo Input::singleSelect('filter_active', null, $controller->_getBooleanSelection());
