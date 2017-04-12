<?php translate("hello_name", array("%firstname%" => ViewBag::get("firstname"), "%lastname%"=>ViewBag::get("lastname")));?>

<?php translate("password_reset_part1", array("%ip%" => ViewBag::get("ip"), "%domain%"=>get_domain()));?>

<?php translate("password_reset_part2");?>
<?php Template::escape(ViewBag::get("url"));?>

<?php translate("password_reset_part3");?>
