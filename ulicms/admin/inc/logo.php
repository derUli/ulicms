<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	if ($acl->hasPermission ( "logo" )) {
		?>
		<?php
		
		if ($_GET ["error"] == "to_big") {
			?>
<p style="color: red; font-size: 1.2em">
<?php translate("uploaded_image_too_big");?></p>
<?php
		}
		?>
<p>
<?php translate("logo_infotext");?>
</p>
<form enctype="multipart/form-data"
	action="index.php?action=logo_upload" method="post">
	<?php
		
		csrf_token_html ();
		?>
	<table border="0" height="250">
		<tr>
			<td><strong><?php translate("your_logo");?>
			</strong></td>
			<td><?php
		
		$logo_path = "../content/images/" . Settings::get ( "logo_image" );
		if (file_exists ( $logo_path ) and is_file ( $logo_path )) {
			echo '<img class="website_logo" src="' . $logo_path . '" alt="' . Settings::get ( "homepage_title" ) . '"/>';
		}
		?>
			</td>
		
		
		<tr>
			<td width=480><strong><?php translate("upload_new_logo");?>
			</strong></td>
			<td><input name="logo_upload_file" type="file"> <br /></td>
		
		
		<tr>
			<td></td>
			<td style="text-align: center"><input type="submit"
				value="<?php translate("upload");?>"></td>
		</tr>
	</table>

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
		noperms ();
	}
}

?>