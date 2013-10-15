<?php 
// neue Config-Variablen anlegen
setconfig("mailer", "php-mail");
setconfig("cache_type", "file");

// Das Script versucht sich selbst zu löschen
@unlink("update.php");

// Zurück ins Backend
header ("Location: admin/");
exit();
?>