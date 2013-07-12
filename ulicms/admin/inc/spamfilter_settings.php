<h1>Spamfilter</h1>

<?php if($_SESSION["group"] >= 40){
     ?>
<form id="spamfilter_settings" name="?action=spam_filter" method="post">
<input type="checkbox" name="spamfilter_enabled"<?php if(getconfig("spamfilter_enabled") == "yes"){
         echo " checked";
         }
     ?> value="yes" onChange="spamFilterEnabledcheckboxChanged(this.checked)"> Spamfilter aktivieren
<script type="text/javascript">
function spamFilterEnabledcheckboxChanged(checked){
    if(checked){
       $('#country_filter_settings').slideDown()
      
    }
    else
    {
       $('#country_filter_settings').slideUp()  
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
Schwarze Liste:<br/>
<textarea name="spamfilter_words_blacklist" rows=10 cols=40><?php
     echo htmlspecialchars(implode(
            explode("||", getconfig("spamfilter_words_blacklist")),
             "\n"
            ), ENT_QUOTES, "UTF-8");
     ?></textarea>

<br/><br/>

Besucher aus folgenden Ländern dürfen <strong>nicht</strong> kommentieren:<br/>
<input type="text" name="country_blacklist" value="<?php echo htmlspecialchars(getconfig("country_blacklist"));
     ?>">
</div>
<br/>
<br/>

<input type="submit" name="submit_spamfilter_settings" value="Einstellungen Speichern"/>
</form>

<script type="text/javascript">
$("#spamfilter_settings").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
  }
  

}); 

</script>

<?php
     }
else{
     echo "<p>Zugriff verweigert!</p>";
     }
