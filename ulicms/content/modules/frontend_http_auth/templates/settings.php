<form
	action="<?php
	echo ModuleHelper::buildAdminURL ( "frontend_http_auth" );
	?>"
	method="post" autocomplete="off">
	<?php
	if (Request::hasVar ( "save" )) {
		?>
<div class="alert alert-success alert-dismissible fade in" role="alert">
		<button type="button" class="close" data-dismiss="alert"
			aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
  <?php translate("changes_was_saved");?>
</div>

<?php }?>
<?php csrf_token_html();?>
	<div class="checkbox">
		<label> <input type="checkbox" name="frontend_http_auth_enable"
			value="1"
			<?php if(Settings::get("frontend_http_auth_enable")) echo "checked";?>>
			<?php translate("frontend_http_auth_enable");?>
	</label>
	</div>
	<p>
		<strong><?php translate("frontend_http_auth_dialog_message")?></strong>
		<br /> <input type="text" name="frontend_http_auth_dialog_message"
			value="<?php Template::escape(Settings::get("frontend_http_auth_dialog_message"));?>"
			maxlength="200">
	</p>
	<p>
		<strong><?php translate("frontend_http_auth_user");?></strong><br /> <input
			type="text" name="frontend_http_auth_user"
			value="<?php Template::escape(Settings::get("frontend_http_auth_user"));?>"
			maxlength=200 autocomplete="new-password">
	</p>
	<p>
		<strong><?php translate("frontend_http_auth_password");?></strong><br />
		<input type="password" name="frontend_http_auth_password"
			value="<?php Template::escape(Settings::get("frontend_http_auth_password"));?>"
			maxlength=200 autocomplete="new-password">
	</p>
	<p class="voffset3">
		<button type="submit" class="btn btn-warning"><?php translate("save");?></button>
	</p>
</form>