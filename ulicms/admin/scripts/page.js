function showAndHideFieldsByType() {
	if ($("#type_list").is(":checked")) {
		$("#tab-list").slideDown();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideDown();
		$("#tab-og").slideDown();
		$("#content-editor").slideDown();
		$("#tab-module").slideUp();
		$("#tab-video").slideUp();
		$("#tab-audio").slideUp();
		$("#tab-text-position").slideDown();
	} else if ($("#type_link").is(":checked")) {
		$("#tab-list").slideUp();
		$("#tab-link").slideDown();
		$("#tab-metadata").slideUp();
		$("#tab-og").slideUp();
		$("#content-editor").slideUp();
		$("#tab-module").slideUp();
		$("#tab-video").slideUp();
		$("#tab-audio").slideUp();
		$("#tab-text-position").slideUp();
	} else if ($("#type_module").is(":checked")) {
		$("#tab-list").slideUp();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideDown();
		$("#tab-og").slideDown();
		$("#content-editor").slideDown();
		$("#tab-module").slideDown();
		$("#tab-video").slideUp();
		$("#tab-audio").slideUp();
		$("#tab-text-position").slideDown();
	} else if ($("#type_video").is(":checked")) {
		$("#tab-list").slideUp();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideDown();
		$("#tab-og").slideDown();
		$("#content-editor").slideDown();
		$("#tab-module").slideUp();
		$("#tab-video").slideDown();
		$("#tab-audio").slideUp();
		$("#tab-text-position").slideDown();
	} else if ($("#type_audio").is(":checked")) {
		$("#tab-list").slideUp();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideDown();
		$("#tab-og").slideDown();
		$("#content-editor").slideDown();
		$("#tab-module").slideUp();
		$("#tab-video").slideUp();
		$("#tab-audio").slideDown();
		$("#tab-text-position").slideDown();
	} else {
		$("#tab-list").slideUp();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideDown();
		$("#tab-og").slideDown();
		$("#content-editor").slideDown();
		$("#tab-module").slideUp();
		$("#tab-video").slideUp();
		$("#tab-audio").slideUp();
		$("#tab-text-position").slideUp();
	}
}

$("input[name=\"type\"]").change(showAndHideFieldsByType);
$(document).ready(showAndHideFieldsByType);

function systemname_vorschlagen(txt) {
	var systemname = txt.toLowerCase();
	systemname = systemname.replace(/ü/g, "ue");
	systemname = systemname.replace(/ö/g, "oe");
	systemname = systemname.replace(/ä/g, "ae");
	systemname = systemname.replace(/Ã/g, "ss");
	systemname = systemname.replace(/\040/g, "_");
	systemname = systemname.replace(/\?/g, "");
	systemname = systemname.replace(/\!/g, "");
	systemname = systemname.replace(/\"/g, "");
	systemname = systemname.replace(/\'/g, "");
	systemname = systemname.replace(/\+/g, "");
	systemname = systemname.replace(/\&/g, "");
	systemname = systemname.replace(/\#/g, "");
	$("#system_title").val(systemname);
}