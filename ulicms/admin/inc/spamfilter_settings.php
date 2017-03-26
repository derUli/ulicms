<h1><?php translate("spamfilter");?></h1>

<?php

if ($acl->hasPermission ( "spam_filter" )) {
	?>
<form id="spamfilter_settings" name="?action=spam_filter" method="post">
<?php
	
	csrf_token_html ();
	?>
	<input type="checkbox" id="spamfilter_enabled"
		name="spamfilter_enabled"
		<?php
	
	if (Settings::get ( "spamfilter_enabled" ) == "yes") {
		echo " checked";
	}
	?>
		value="yes" onChange="spamFilterEnabledcheckboxChanged(this.checked)">
	<label for="spamfilter_enabled"><?php translate("spamfilter_enabled");?>
	</label>
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

	<div id="country_filter_settings"
		<?php
	if (Settings::get ( "spamfilter_enabled" ) != "yes") {
		echo " style='display:none;'";
	}
	?>>
		<br />
		<?php translate("blacklist");?>
		<br />
		<textarea name="spamfilter_words_blacklist" rows=10 cols=40><?php
	echo htmlspecialchars ( implode ( explode ( "||", Settings::get ( "spamfilter_words_blacklist" ) ), "\n" ), ENT_QUOTES, "UTF-8" );
	?></textarea>
		<br /> <br />

		<?php translate("spam_countries");?>
		<br /> <input type="text" name="country_blacklist"
			value="<?php
	
	echo htmlspecialchars ( Settings::get ( "country_blacklist" ) );
	?>"> <br /> <br /> <input type="checkbox" name="disallow_chinese_chars"
			id="disallow_chinese_chars"
			<?php
	if (Settings::get ( "disallow_chinese_chars" )) {
		echo " checked=\"checked\"";
	}
	?>> <label for="disallow_chinese_chars"><?php translate("disallow_chinese_chars");?>
		</label> <br /> <br /> <input type="checkbox"
			name="check_for_spamhaus" value="yes" id="check_for_spamhaus"
			<?php
	if (Settings::get ( "check_for_spamhaus" )) {
		echo " checked=\"checked\"";
	}
	?>> <label for="check_for_spamhaus"><?php translate("check_for_spamhaus");?>
		</label>

	</div>
	<br /> <br /> <input type="submit" name="submit_spamfilter_settings"
		value="<?php translate("save_changes");?>" />
	<?php
	if (Settings::get ( "override_shortcuts" ) == "on" || Settings::get ( "override_shortcuts" ) == "backend") {
		?>
	<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
	}
	?>
</form>

<script type="text/javascript">
$("#spamfilter_settings").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\"><?php
	translate ( "changes_was_saved" )?></span>");
  }
  

}); 

</script>

<?php
} else {
	noperms ();
}
