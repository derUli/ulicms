<?php
include_once "init.php";

setconfig("locale_de", "de_DE.UTF-8; de_DE; deu_deu");
setconfig("locale_en", "en_US.UTF-8; en_GB.UTF-8; en_US; en_GB; english-uk; eng; uk");

setconfig("db_schema_version", "8.0.1");

// @unlink("update.php");
ulicms_redirect("admin/");