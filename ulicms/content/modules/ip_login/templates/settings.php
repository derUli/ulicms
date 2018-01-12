<?php if(Request::getVar("save") == "1"){?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate("changes_was_saved")?>
		</div>
<?php }?>
<?php echo ModuleHelper::buildMethodCallForm("IpLoginSettings", "save");?>
<p>
	<strong><?php translate("ip_user_mapping");?></strong><br /> <br />
<?php echo nl2br(get_translation("ip_user_mapping_help", array("%example%"=>'<code>'.ViewBag::get("example").'</code>')));;?>
<br />
	<textarea name="ip_user_login" rows="8"><?php esc(Settings::get ( "ip_user_login" ));?></textarea>
</p>
<p>
	<button type="submit" class="btn btn-success"><?php translate("save");?></button>
<?php echo ModuleHelper::endForm();?>