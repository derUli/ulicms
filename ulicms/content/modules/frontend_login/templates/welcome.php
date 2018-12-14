<?php
$user = new User();
$user->loadById(get_user_id());
?>
<p>
<?php

secure_translate("hello_lastname_firstname", array(
    "%lastname%" => $user->getLastname(),
    "%firstname%" => $user->getFirstname()
));
?></p>
<p>
<?php

echo ModuleHelper::buildMethodCallButton(FrontendLoginController::class, "doLogout", get_translation("logout"), array(
    "class" => "btn btn-default"
));
?></p>