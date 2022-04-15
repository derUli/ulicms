<?php

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\HTML\Input;
use UliCMS\HTML\ListItem;

$controller = ControllerRegistry::get("PageController");
$placeholder = new ListItem("all", "[" . get_translation("all") . "]");
$none = new ListItem("0", "[" . get_translation("none") . "]");
?>

<?php translate("parent_id"); ?>
<?php

echo Input::singleSelect("filter_parent", "0", [$placeholder, $none]);
