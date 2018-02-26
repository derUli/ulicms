
<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("settings_categories");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate("spamfilter");?></h1>
<?php
$acl = new ACL ();
if ($acl->hasPermission ( "spam_filter" )) {
	?>
<form id="spamfilter_settings" name="?action=spam_filter" method="post">
<?php echo ModuleHelper::buildMethodCallForm("SpamFilterController", "save");?>
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
		<br /> 

		<?php translate("spam_countries");?>
		<br /> <input type="text" name="country_blacklist"
			value="<?php
	
	echo htmlspecialchars ( Settings::get ( "country_blacklist" ) );
	?>"> <br /> <input type="checkbox" name="disallow_chinese_chars"
			id="disallow_chinese_chars"
			<?php
	if (Settings::get ( "disallow_chinese_chars" )) {
		echo " checked=\"checked\"";
	}
	?>> <label for="disallow_chinese_chars"><?php translate("disallow_chinese_chars");?>
		</label> <br /> <br /> <input type="checkbox"
			name="disallow_cyrillic_chars" id="disallow_cyrillic_chars"
			<?php
	if (Settings::get ( "disallow_cyrillic_chars" )) {
		echo " checked=\"checked\"";
	}
	?>> <label for="disallow_cyrillic_chars"><?php translate("disallow_cyrillic_chars");?>
		</label> <br /> <br />
		<p>
			<label for="min_time_to_fill_form"><?php translate("min_time_to_fill_form");?></label><br />
			<input type="number" name="min_time_to_fill_form"
				id="min_time_to_fill_form" step="any" min="0"
				max="<?php esc(PHP_INT_MAX );?>"
				value="<?php
	
	esc ( Settings::get ( "min_time_to_fill_form", "int" ) );
	?>">
		</p>

	</div>

	<p>
		<button type="submit" name="submit_spamfilter_settings"
			class="btn btn-primary voffset3"><?php
	
	translate ( "save_changes" );
	?></button>

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
