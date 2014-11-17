<?php 
define("TRANSLATION_TITLE", "UliCMS Installation");
define("TRANSLATION_WELCOME", "Willkommen");
define("TRANSLATION_WELCOME2", "Willkommen zur Installation von UliCMS.");
define("TRANSLATION_BETA_VERSION", "Das hier ist eine Vorab-Version von UliCMS.<br/>
Das bedeutet, diese Version ist noch nicht 100-prozentig fertig und dient nur der Vorschau auf ein neues Release.<br/>
Setzen Sie diese Version bitte nicht produktiv ein!");
define("TRANSLATION_FOLLOW_INSTRUCTIONS", "Folgen Sie den Anweisungen um das CMS auf Ihrem Server zu installieren.");
define("TRANSLATION_CHMOD", "<p>Setzen Sie bitte vorher die Dateirechte der folgenden Dateien auf 0755.<br/>
<ol>
<li>Das Hauptverzeichnis des CMS (Ohne die Unterordner)</li>
<li>templates/ (inklusive Inhalt und Unterordner)</li>
<li>content/ (inklusive Inhalt und Unterordner)</li>
<li>modules/ (inklusive Inhalt und Unterordner)</li>
</ol>
</p>");
define("TRANSLATION_PERMISSION", "So müssen die Berechtigungen gesetzt sein");
define("TRANSLATION_PERMISSIONS2", "FTP Rechtevergabe");
define("TRANSLATION_GD_MISSING", "<strong>php5-gd</strong> ist nicht installiert.<br/>Ohne <strong>php5-gd</strong> lässt sich UliCMS zwar installieren,<br/>Bitte installieren Sie dieses PHP-Modul und versuchen Sie es erneut.");
define("TRANSLATION_MYSQLI_MISSING", "<strong>php5-mysql</strong> ist nicht installiert.<br/>Bitte installieren Sie dieses PHP-Modul und versuchen Sie es erneut.");
define("TRANSLATION_JSON_MISSING", "<strong>php5-json</strong> ist nicht installiert.<br/>Bitte installieren Sie zuerst php5-json und versuchen Sie es dann erneut.");
define("TRANSLATION_NEXT", "Weiter");
define("TRANSLATION_MYSQL_LOGIN", "MySQL Logindaten");
define("TRANSLATION_MYSQL_LOGIN_HELP", "Bitte tragen Sie in das untere Formular die Logindaten für den MySQL-Server ein. Diese bekommen Sie von Ihrem Provider.");

define("TRANSLATION_SERVERNAME", "Servername");
define("TRANSLATION_LOGINNAME", "Benutzername");
define("TRANSLATION_PASSWORD", "Passwort");
define("TRANSLATION_DATABASE_NAME", "Datenbankname");
define("TRANSLATION_PREFIX", "Prefix");
define("TRANSLATION_DB_CONNECTION_FAILED", "Die Verbindung mit dem MySQL-Datenbankserver konnte nicht hergestellt werden.<br/>Dies kann z.B. an einem falschen Passwort liegen. Wenn Sie sich sicher sind, dass das Passwort richtig ist, prüfen Sie ob der MySQL-Datenbankserver läuft und erreichbar ist.");