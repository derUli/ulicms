<?php
include_once "init.php";
// neue Config-Variablen anlegen
setconfig("mailer", "php-mail");
setconfig("cache_type", "file");
setconfig("registered_user_default_level", "10");
setconfig("override_shortcuts", "backend");

setconfig("db_schema_version", "6.7");

// Das Script versucht sich selbst zu löschen
@unlink("update.php");

// Zurück ins Backend
header ("Location: admin/");
exit();
?>
