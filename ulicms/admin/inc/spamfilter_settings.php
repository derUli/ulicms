<h1><?php echo TRANSLATION_SPAMFILTER;?></h1>

<?php if($acl -> hasPermission("spam_filter")){
     ?>
<form id="spamfilter_settings" name="?action=spam_filter" method="post">
<input type="checkbox" id="spamfilter_enabled" name="spamfilter_enabled"<?php if(getconfig("spamfilter_enabled") == "yes"){
         echo " checked";
         }
     ?> value="yes" onChange="spamFilterEnabledcheckboxChanged(this.checked)"> <label for="spamfilter_enabled"><?php echo TRANSLATION_ENABLED;?></label>
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
<?php echo TRANSLATION_BLACKLIST;?><br/>
<textarea name="spamfilter_words_blacklist" rows=10 cols=40><?php
     echo htmlspecialchars(implode(
            explode("||", getconfig("spamfilter_words_blacklist")),
             "\n"
            ), ENT_QUOTES, "UTF-8");
     ?></textarea>

<br/><br/>

<?php echo TRANSLATION_SPAM_COUNTRIES;?><br/>
<input type="text" name="country_blacklist" value="<?php echo htmlspecialchars(getconfig("country_blacklist"));
     ?>">
<br/><br/>
<input type="checkbox" name="disallow_chinese_chars" id="disallow_chinese_chars" <?php
    if(getconfig("disallow_chinese_chars")) echo " checked=\"checked\"";
    ?>> <label for="disallow_chinese_chars"><?php echo TRANSLATION_DISALLOW_CHINESE_CHARS;?></label>

</div>
<br/><br/>

<input type="submit" name="submit_spamfilter_settings" value="<?php echo TRANSLATION_SAVE_CHANGES;?>"/>
<?php
     if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
         ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
     ?>
</form>

<script type="text/javascript">
$("#spamfilter_settings").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\"><?php echo TRANSLATION_CHANGES_WAS_SAVED;?></span>");
  }
  

}); 

</script>

<?php
     }
else{
     noperms();
     }
