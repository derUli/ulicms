
<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("settings_categories");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate("spamfilter");?></h1>
<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("spam_filter")) {
    ?>
<form id="spamfilter_settings" name="?action=spam_filter" method="post">
<?php echo ModuleHelper::buildMethodCallForm("SpamFilterController", "save");?>


		<div class="checkbox">
		<label for="spamfilter_enabled"> <input type="checkbox"
			id="spamfilter_enabled" name="spamfilter_enabled"
			<?php
    
    if (Settings::get("spamfilter_enabled") == "yes") {
        echo " checked";
    }
    ?>
			value="yes" onChange="spamFilterEnabledcheckboxChanged(this.checked)">
<?php translate("spamfilter_enabled");?>
	</label>
	</div>
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
    if (Settings::get("spamfilter_enabled") != "yes") {
        echo " style='display:none;'";
    }
    ?>>
		<label for="spamfilter_words_blacklist"><?php translate("blacklist");?></label><br />
		<textarea name="spamfilter_words_blacklist"
			id="spamfilter_words_blacklist" rows=10 cols=40><?php
    echo htmlspecialchars(Settings::get("spamfilter_words_blacklist"), ENT_QUOTES, "UTF-8");
    ?></textarea>
		<br /> <label for="country_blacklist"><?php translate("spam_countries");?></label>
		<input type="text" name="country_blacklist" id="country_blacklist"
			value="<?php
    
    echo htmlspecialchars(Settings::get("country_blacklist"));
    ?>">

		<div class="checkbox">
			<label for="disallow_chinese_chars"> <input type="checkbox"
				name="disallow_chinese_chars" id="disallow_chinese_chars"
				<?php
    if (Settings::get("disallow_chinese_chars")) {
        echo " checked=\"checked\"";
    }
    ?>> <?php translate("disallow_chinese_chars");?>
		</label>
		</div>
		<div class="checkbox">
			<label for="disallow_cyrillic_chars"> <input type="checkbox"
				name="disallow_cyrillic_chars" id="disallow_cyrillic_chars"
				<?php
    if (Settings::get("disallow_cyrillic_chars")) {
        echo " checked=\"checked\"";
    }
    ?>> <?php translate("disallow_cyrillic_chars");?>
		</label>
		</div>
		<div class="checkbox">
			<label><input name="reject_requests_from_bots" type="checkbox"
				value=""
				<?php
    
    echo Settings::get("reject_requests_from_bots") ? "checked" : "";
    ?>><?php translate("reject_requests_from_bots");?></label>
		</div>
		<div class="checkbox">
			<label for="check_mx_of_mail_address"> <input type="checkbox"
				name="check_mx_of_mail_address" id="check_mx_of_mail_address"
				<?php
    if (Settings::get("check_mx_of_mail_address")) {
        echo " checked=\"checked\"";
    }
    ?>> <?php translate("check_mx_of_mail_address");?>
		</label>
		</div>
		<p>
			<label for="min_time_to_fill_form"><?php translate("min_time_to_fill_form");?></label><br />
			<input type="number" name="min_time_to_fill_form"
				id="min_time_to_fill_form" step="any" min="0"
				max="<?php esc(PHP_INT_MAX );?>"
				value="<?php
    
    esc(Settings::get("min_time_to_fill_form", "int"));
    ?>">
		</p>
	</div>
	<p>
		<button type="submit" name="submit_spamfilter_settings"
			class="btn btn-primary voffset3"><?php
    
    translate("save_changes");
    ?></button>
	</p>
</form>
<script type="text/javascript">
$("#spamfilter_settings").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\"><?php
    translate("changes_was_saved")?></span>");
  }
});
</script>

<?php
} else {
    noPerms();
}
