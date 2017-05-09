<?php
define ( "MODULE_ADMIN_HEADLINE", get_translation ( "oneclick_upgrade" ) . " " . get_translation ( "settings" ) );
function oneclick_upgrade_admin() {
	if (get_request_method () == "POST") {
		Settings::set ( "oneclick_upgrade_skip_kcfinder", intval ( isset ( $_POST ["oneclick_upgrade_skip_kcfinder"] ) ) );
	}
	$oneclick_upgrade_skip_kcfinder = intval ( Settings::get ( "oneclick_upgrade_skip_kcfinder" ) );
	?>
	<?php
	if (get_request_method () == "POST") {
		
		?>
<div class="alert alert-success alert-dismissable fade in">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate("changes_was_saved")?>
		</div>
<?php
	}
	?>
<form action="<?php Template::escape(getModuleAdminSelfPath());?>"
	method="post">
	<?php csrf_token_html();?>
	<div class="checkbox">
		<label><input type="checkbox" name="oneclick_upgrade_skip_kcfinder"
			value="1" <?php if($oneclick_upgrade_skip_kcfinder) echo "checked"?>><?php translate("skip_kcfinder");?></label>
	</div>

	<button type="submit" class="btn btn-default"><?php translate("save");?></button>
</form>
<?php
}
?>