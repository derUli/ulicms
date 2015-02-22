<?php
include_once "init.php";

setconfig("locale_de", "de_DE.UTF-8");
setconfig("locale_en", "en_US.UTF-8");

// @unlink("update.php");
ulicms_redirect("admin/");
