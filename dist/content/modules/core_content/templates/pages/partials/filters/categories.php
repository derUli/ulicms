<?php

use App\HTML\Input;

$controller = ControllerRegistry::get("PageController");

translate("category");
echo Input::singleSelect("filter_category", null, $controller->_getCategorySelection());
