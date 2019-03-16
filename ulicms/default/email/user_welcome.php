<?php 
$user = ViewBag::get("user");

translate("hello_x", array("%x%"=> $user->getFirstname() )); ?>,

<?php translate("admin_created_an_account", array("%url%"=> ViewBag::get("url"))); ?>


<?php translate("here_are_your_credentials"); ?>

<?php translate("username");?>: <?php esc($user->getUserName());?>

<?php translate("password");?>: <?php esc(ViewBag::get("password"));?>