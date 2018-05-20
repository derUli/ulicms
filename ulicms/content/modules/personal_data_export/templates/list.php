<form
	action="<?php echo Modulehelper::buildAdminURL("personal_data_export");?>"
	autocomplete="off">
<?php csrf_token_html();?>
<p>
		<strong><?php translate("name_or_email_address");?></strong> <br /> <input
			type="search" name="search" value="">
	</p>
	<button type="submit" class="btn btn-primary"><?php translate("search");?></button>
</form>