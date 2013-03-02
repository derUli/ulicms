<h1>Cache</h1>
<?php 
if($_SESSION["group"] >= 20){
?>
<?php 
if(isset($_GET["clear_cache"])){?>
<p style="color:green;">Der Cache wurde erfolgreich geleert.</p>
<?php }?>
<p>Um die Performance der Website zu verbessern, 
bietet das UliCMS eine Cache-Funktion.<br/>
Statische Seiten, die keine Module enthalten, werden einmalig generiert und dann im cache-Ordner zwischengespeichert.
Anschließend werden statt die Inhalte immer wieder aus der Datenbank zu laden, die Inhalte aus den gespeicherten HTML-Dateien geladen.</p>
<p><strong>Aktueller Status des Caches:</strong><br/>
<?php if(!getconfig("cache_disabled")){?>
<span style="color:green;">aktiv</span></p>

<p>Sie können den Cache deaktivieren, in dem Sie in den Einstellungen im Expertenmodus die Konfigurationsvariable<br/>
<code>cache_disabled</code><br/>
Mit einem beliebigen Wert anlegen.</p>

<p>Wenn Änderungen an der Website vorgenommen wurden sind, ist es erforderlich, den Cache zu leeren, damit die Änderungen auch auf der Website angezeigt werden.</p>
<form post="index.php" method="get">
<input type="hidden" name="action" value="cache"/>
<input type="hidden" name="clear_cache" value="yes"/>
<input type="submit" value="Cache leeren"/>

</form>

<?php } else{ ?>
<span style="color:red;">deaktiviert</span></p>
<p>Sie können den Cache aktivieren, in dem Sie in den Einstellungen im Expertenmodus die Konfigurationsvariable<br/>
<code>cache_disabled</code><br/>
löschen.</p>
<?php }?>

<?php }else{

echo "<p>Zugriff verweigert!</p>";
}

?>