<?php 
// Zusätzliche Funktionen bitte hier eintragen
mysql_query("INSERT INTO `".$prefix."content` (notinfeed, systemname, title, content, active,
created, lastchangeby, autor, views, comments_enabled, redirection, menu, position, parent)
VALUES (0, 'willkommen', 'Willkommen', 
'<p>Herzlichen Glückwunsch!<br/>
UliCMS wurde erfolgreich auf dieser Website installiert.</p>', 1, ".time().",
1, 1, 0, 0, '', 'top', 0, '-')
");
?>