<?php
define("MODULE_ADMIN_HEADLINE", get_translation("oneclick_upgrade") . " " . get_translation("settings"));

function oneclick_upgrade_admin()
{
    if (get_request_method() == "POST") {
        Settings::set("oneclick_upgrade_channel", strval($_POST["oneclick_upgrade_channel"]));
    }
    $oneclick_upgrade_channel = strval(Settings::get("oneclick_upgrade_channel"));
    $channels = array(
        "fast",
        "slow",
    );
    ?>
	<?php
if (get_request_method() == "POST") {

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
	<div>
		<label for="oneclick_upgrade_channel"><?php translate("channel")?></label><br />
		<select name="oneclick_upgrade_channel" size=1>
			<?php for ($i = 0; $i < count($channels); $i++) {?>
			<option value="<?php Template::escape($channels[$i])?>"
				<?php if ($oneclick_upgrade_channel == $channels[$i]) {
        echo " selected";
    }
        ?>><?php Template::escape(get_translation($channels[$i]))?></option>
			<?php }?>

		</select>
	</div>


	<button type="submit" class="btn btn-primary voffset3">
		<i class="fa fa-save"></i> <?php translate("save");?></button>
</form>
<?php
}
?>