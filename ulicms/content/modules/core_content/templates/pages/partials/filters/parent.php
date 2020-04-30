<?php

use UliCMS\HTML\Input;
use UliCMS\HTML\ListItem;

$controller = ControllerRegistry::get("PageController");
$placeholder = new ListItem(null, "[" . get_translation("all") . "]");
?>

<?php translate("parent_id"); ?>
<?php

echo Input::singleSelect("filter_parent", null, [$placeholder]);
