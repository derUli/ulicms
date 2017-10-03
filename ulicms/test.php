<?php
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Text, der gesendet wird, falls der Benutzer auf Abbrechen drückt';
    exit;
} else {
    echo "<p>Hallo {$_SERVER['PHP_AUTH_USER']}.</p>";
    echo "<p>Sie gaben {$_SERVER['PHP_AUTH_PW']} als Passwort ein.</p>";
}
?>