function uninstallModule(url, name) {
	if (confirm(Translation.AskForUninstallPackage.replace("%name%", name))) {
		$.ajax({
			url : url,
			success : function() {
				$("li#dataset-module-" + name).slideUp();

			}
		});
	}
	return false;
}

function uninstallTheme(url, name) {
	if (confirm(Translation.AskForUninstallPackage.replace("%name%", "theme-"
			+ name))) {
		$.ajax({
			url : url,
			success : function() {

				$("li#dataset-theme-" + name).slideUp();

			}
		});
	}
	return false;
}

var ajaxOptions = {
	success : function(responseText, statusText, xhr, $form) {
		$("div#inst_patch_slide_container").slideUp();

	}

}

$("form#truncate_installed_patches").ajaxForm(ajaxOptions);