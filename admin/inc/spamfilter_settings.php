<h1>Spamfilter</h1>

<?php if($_SESSION["group"] >= 40){?>
<form name="?action=spam_filter" method="post">
<input type="checkbox" name="spamfilter_enabled"<?php if(getconfig("spamfilter_enabled") == "yes"){
echo " checked";
}?> value="yes" onChange="spamFilterEnabledcheckboxChanged(this.checked)"> Spamfilter aktivieren
<script type="text/javascript">
function spamFilterEnabledcheckboxChanged(checked){
    div = document.getElementById("country_filter_settings");
    if(checked){
    
    div.style.display = "block";
      
    }
    else{

    div.style.display = "none";    
    }
}
</script>

<div id="country_filter_settings"<?php
if(getconfig("spamfilter_enabled") != "yes"){
  echo " style='display:none;'";
}
?>>
<br/>
<br/>
Besucher aus folgenden Ländern dürfen kommentieren:<br/>
<input type="text" name="country_whitelist" value="<?php echo htmlspecialchars(getconfig("country_whitelist"));?>">
<br/>
<br/>
Besucher aus folgenden Ländern dürfen <strong>nicht</strong> kommentieren:<br/>
<input type="text" name="country_blacklist" value="<?php echo htmlspecialchars(getconfig("country_blacklist"));?>">
</div>
<br/>
<br/>

<input type="submit" name="submit_spamfilter_settings" value="Einstellungen Speichern"/>
</form>

<?php 
}
else{
   echo "<p>Zugriff verweigert!</p>";
}
