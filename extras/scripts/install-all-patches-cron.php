<?php
// Dieses Script in den Hauptordner der UliCMS Installation hochladen
// Dann Den Aufruf dieses Scripts (z.B. http://www.domain.de/install-all-patches-cron.php) als Cronjob einrichten auf https://www.cronjob.de oder einem ähnlichen Dienst
// Dies funktioniert jedoch nur, wenn der Provider die PHP Funktion passthru() nicht gesperrt hat.

// Plaintext, kein HTML
header ( "Content-Type: text/plain; charset=UTF-8" );
// Time Limit deaktivieren, um Abbruch der Patch Installation zu vermeiden
@set_time_limit ( 0 );

// Script auch nach Abbruch des Downloads weiter ausführen, damit Patches fertig installiert werden
@ignore_user_abort ( 1 );

// Alle Patches installieren mit patchck.php
passthru ( "php shell/patchck.php install all" );