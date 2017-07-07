<span id="url"
	data-action-url="<?php Template::escape(ModuleHelper::buildAdminURL("convert_to_utf8mb4", ModuleHelper::buildMethodCall("ConvertToUTF8MB4", "convertTable")));?>"
	data-finish-url="<?php Template::escape(ModuleHelper::buildActionURL("conversion_finished"));?>"></span>
<form action="#" method="get">
	<div id="convert-container">
		<p><?php translate("convert_to_utf8mb4_help_text")?></p>
		<input type="button" id="btn_start_conversion"
			value="<?php translate("start_conversion");?>">
	</div>
</form>
<script type="text/javascript"
	src="<?php Template::escape(ModuleHelper::buildModuleRessourcePath("convert_to_utf8mb4", "js/main.js"));?>"></script>