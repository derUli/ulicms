<?php

use App\HTML\Input;
use App\HTML\ListItem;

$controller = ControllerRegistry::get('PageController');
$placeholder = new ListItem('all', '[' . get_translation('all') . ']');
$none = new ListItem('0', '[' . get_translation('none') . ']');
?>

<?php translate('parent_id'); ?>
<?php

echo Input::singleSelect('filter_parent', '0', [$placeholder, $none]);
