<?php
$acl = new ACL ();
if ($acl->hasPermission ( "cache" )) {
	?>
<h1>Cache</h1>
<?php
	if (isset ( $_GET ["clear_cache"] )) {
		?>
<p style="color: green;">
<?php translate ( "cache_was_cleared" );?>
</p>
<?php
	}
	?>
	<?php translate("cache_text1");?>
<p>
	<strong>Aktueller Status des Caches:</strong><br />
	<?php
	if (! Settings::get ( "cache_disabled" )) {
		?>
	<span style="color: green;"><?php translate("enabled");?></span>
</p>
<?php translate("cache_text3");?>
<?php echo ModuleHelper::buildMethodCallForm("CacheSettingsController", "clearCache")?>
<input type="submit" value="<?php translate("clear_cache");?>" />
<?php
		if (Settings::get ( "override_shortcuts" ) == "on" || Settings::get ( "override_shortcuts" ) == "backend") {
			?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
		}
		?>
</form>

<?php
	} else {
		?>
<p>
	<span style="color: red;"><?php translate("disabled");?></span>
</p>
<?php translate("cache_text2");?>
		<?php
	}
} else {
	noperms ();
}

?>