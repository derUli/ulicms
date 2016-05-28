function showAndHideFieldsByType() {
	if ($("#type_list").is(":checked")) {
		$("#tab-list").slideDown();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideDown();
		$("#tab-og").slideDown();
		$("#content-editor").slideDown();
	} else if ($("#type_link").is(":checked")) {
		$("#tab-list").slideUp();
		$("#tab-link").slideDown();
		$("#tab-metadata").slideUp();
		$("#tab-og").slideUp();
		$("#content-editor").slideUp();
	} else {
		$("#tab-list").slideUp();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideDown();
		$("#tab-og").slideDown();
		$("#content-editor").slideDown();
	}
}

$("input[name=\"type\"]").change(showAndHideFieldsByType);
$(document).ready(showAndHideFieldsByType);