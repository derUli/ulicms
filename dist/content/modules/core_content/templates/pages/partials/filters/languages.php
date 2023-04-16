<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\HTML\Input;

$controller = ControllerRegistry::get('PageController');
?>

<?php translate('language'); ?>
<?php

echo Input::singleSelect('filter_language', null, $controller->_getLanguageSelection());
