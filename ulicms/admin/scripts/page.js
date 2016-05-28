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

function systemname_vorschlagen(txt){
	var systemname=txt.toLowerCase();
	systemname=systemname.replace(/ü/g,"ue");
	systemname=systemname.replace(/ö/g,"oe");
	systemname=systemname.replace(/ä/g,"ae");
	systemname=systemname.replace(/Ã/g,"ss");
	systemname=systemname.replace(/\040/g,"_");
	systemname=systemname.replace(/\?/g,"");
	systemname=systemname.replace(/\!/g,"");
	systemname=systemname.replace(/\"/g,"");
	systemname=systemname.replace(/\'/g,"");
	systemname=systemname.replace(/\+/g,"");
	systemname=systemname.replace(/\&/g,"");
	systemname=systemname.replace(/\#/g,"");
	$("form#editpageform #system_title").val(systemname);
	}