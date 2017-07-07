<div id="convert-container">
	<form
		action="<?php Template::escape(ModuleHelper::buildAdminURL("convert_to_utf8mb4", ModuleHelper::buildMethodCall("ConvertToUTF8MB4", "convertTable")));?>"
		method="post">
	<?php csrf_token_html();?>
	<p><?php translate("convert_to_utf8mb4_help_text")?></p>
		<input type="submit" name="submit"
			value="<?php translate("start_conversion");?>">
	</form>
</div>