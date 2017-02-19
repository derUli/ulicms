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
		$("#tab-image").slideUp();
		$("#tab-text-position").slideDown();
		$("#tab-cache-control").slideDown();
		$("#article-metadata").slideUp();
		$("#article-image").slideUp();
		$("#comment-fields").slideUp();
	} else if ($("#type_link").is(":checked")) {
		$("#tab-list").slideUp();
		$("#tab-link").slideDown();
		$("#tab-metadata").slideUp();
		$("#tab-og").slideUp();
		$("#tab-image").slideUp();
		$("#content-editor").slideUp();
		$("#tab-module").slideUp();
		$("#tab-video").slideUp();
		$("#tab-audio").slideUp();
		$("#tab-text-position").slideUp();
		$("#tab-cache-control").slideUp();
		$("#article-metadata").slideUp();
		$("#article-image").slideUp();
		$("#comment-fields").slideUp();
	} else if ($("#type_node").is(":checked")) {
		$("#tab-list").slideUp();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideUp();
		$("#tab-og").slideUp();
		$("#tab-image").slideUp();
		$("#content-editor").slideUp();
		$("#tab-module").slideUp();
		$("#tab-video").slideUp();
		$("#tab-audio").slideUp();
		$("#tab-text-position").slideUp();
		$("#tab-cache-control").slideUp();
		$("#article-metadata").slideUp();
		$("#article-image").slideUp();
		$("#comment-fields").slideUp();
	} else if ($("#type_module").is(":checked")) {
		$("#tab-list").slideUp();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideDown();
		$("#tab-og").slideDown();
		$("#content-editor").slideDown();
		$("#tab-module").slideDown();
		$("#tab-video").slideUp();
		$("#tab-image").slideUp();
		$("#tab-audio").slideUp();
		$("#tab-text-position").slideDown();
		$("#tab-cache-control").slideDown();
		$("#article-metadata").slideUp();
		$("#article-image").slideUp();
		$("#comment-fields").slideUp();
	} else if ($("#type_video").is(":checked")) {
		$("#tab-list").slideUp();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideDown();
		$("#tab-image").slideUp();
		$("#tab-og").slideDown();
		$("#content-editor").slideDown();
		$("#tab-module").slideUp();
		$("#tab-video").slideDown();
		$("#tab-audio").slideUp();
		$("#tab-text-position").slideDown();
		$("#tab-cache-control").slideDown();
		$("#article-metadata").slideUp();
		$("#article-image").slideUp();
		$("#comment-fields").slideUp();
	} else if ($("#type_audio").is(":checked")) {
		$("#tab-list").slideUp();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideDown();
		$("#tab-og").slideDown();
		$("#content-editor").slideDown();
		$("#tab-module").slideUp();
		$("#tab-image").slideUp();
		$("#tab-video").slideUp();
		$("#tab-audio").slideDown();
		$("#tab-cache-control").slideDown();
		$("#tab-text-position").slideDown();
		$("#article-metadata").slideUp();
		$("#article-image").slideUp();
		$("#comment-fields").slideUp();
	} else if ($("#type_image").is(":checked")) {
		$("#tab-list").slideUp();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideDown();
		$("#tab-og").slideDown();
		$("#content-editor").slideDown();
		$("#tab-module").slideUp();
		$("#tab-video").slideUp();
		$("#tab-audio").slideUp();
		$("#tab-image").slideDown();
		$("#tab-text-position").slideDown();
		$("#tab-cache-control").slideDown();
		$("#article-metadata").slideUp();
		$("#article-image").slideUp();
		$("#comment-fields").slideUp();
	} else {
		$("#tab-list").slideUp();
		$("#tab-link").slideUp();
		$("#tab-metadata").slideDown();
		$("#tab-og").slideDown();
		$("#tab-image").slideUp();
		$("#content-editor").slideDown();
		$("#tab-module").slideUp();
		$("#tab-video").slideUp();
		$("#tab-audio").slideUp();
		$("#tab-text-position").slideUp();
		$("#tab-cache-control").slideDown();
		$("#comment-fields").slideUp();
		if ($("#type_article").is(":checked")) {
			$("#article-metadata").slideDown();
			$("#article-image").slideDown();
		} else if ($("#type_comment").is(":checked")) {
			$("#article-metadata").slideDown();
			$("#article-image").slideUp();
			$("#comment-fields").slideDown();
		} else {
			$("#article-metadata").slideUp();
			$("#article-image").slideUp();
		}
	}
	$(".custom-field-tab").each(function(index, el) {
		if ($(el).data("type") == $("input[name='type']:checked").val()) {
			$(el).slideDown();
		} else {
			$(el).slideUp();
		}

	});
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

function systemnameOrLanguageChanged(item) {
	var id_field = $("input[name='page_id']");
	var myid = 0;
	if (id_field) {
		myid = $(id_field).val();
	}
	var data = {
		ajax_cmd : "check_if_systemname_is_free",
		systemname : $("input[name='system_title']").val(),
		language : $("select[name='language']").val(),
		id : myid
	};
	$.post("index.php", data, function(text, status) {
		if (text == "yes") {
			$("input[name='system_title']").removeClass("error-field");
			$("select[name='language']").removeClass("error-field");
		} else {
			$("input[name='system_title']").addClass("error-field");
			$("select[name='language']").addClass("error-field");
		}
	});

}

function filterParentPages() {
	var data = {
		ajax_cmd : "getPageListByLang",
		mlang : $("select[name='language']").val(),
		mmenu : $("select[name='menu']").val(),
		mparent : $("select[name='parent']").val()
	};

	$.post("index.php", data, function(text, status) {
		$("select[name='parent']").html(text);
	});
}

$(function() {
	$("input[name='system_title']").keyup(function() {
		systemnameOrLanguageChanged($(this));
	});
	$("select[name='menu']").change(function() {
		filterParentPages();
	});

	$("select[name='language']").change(function() {
		systemnameOrLanguageChanged($(this));
		filterParentPages();
	});

	systemnameOrLanguageChanged($("input[name='system_title']"));
	systemnameOrLanguageChanged($("select[name='language']"));

	filterParentPages();
});