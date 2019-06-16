<?php translate("hello_name", array("%firstname%" => ViewBag::get("firstname"), "%lastname%" => ViewBag::get("lastname"))); ?>


<?php translate("password_reset1", array("%ip%" => ViewBag::get("ip"), "%domain%" => get_domain())); ?>


<?php translate("password_reset2"); ?>


<?php echo ViewBag::get("url"); ?>


<?php translate("password_reset3"); ?>
