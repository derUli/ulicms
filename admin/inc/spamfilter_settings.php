<h1>Spamfilter</h1>
<?php if($_SESSION["group"] >= 40){?>
<form name="?action=spam_filter" method="post">
<input type="checkbox" name="spamfilter_enabled"<?php if(getconfig("spamfilter_enabled") == "yes"){
echo " checked";
}?> value="yes"> Spamfilter aktivieren
<br/>
<br/>
<input type="submit" name="submit_spamfilter_settings" value="Einstellungen Speichern"/>
</form>

<?php 
}
else{
   echo "<p>Zugriff verweigert!</p>";
}