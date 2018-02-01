function showAndHideFieldsByType() {
	var type = $('input[name=type]:checked').val()
	$(".typedep").slideUp();
	var typeData = AllTypes[type];
	var show = typeData["show"];

	for (i = 0; i < show.length; i++) {
		$(show[i]).slideDown();
	}

	if ($("#type_snippet").is(":checked")) {
		$("select[name='hidden']").val("1");
		$("select[name='menu']").val("not_in_menu");
	}

	$(".custom-field-tab").each(function(index, el) {
		if ($(el).data("type") == $("input[name='type']:checked").val()) {
			$(el).slideDown();
		} else {
			$(el).slideUp();
		}

	});

	if ($("#type_node").is(":checked") || $("#type_snippet").is(":checked")) {
		$("#btn-view-page").slideUp();
	} else {
		$("#btn-view-page").slideDown();
	}

	if ($("select[name='menu']").val() == "none") {
		$("#parent-div").slideUp();
	} else {

		$("#parent-div").slideDown();
	}
}

$("input[name=\"type\"]").change(showAndHideFieldsByType);
$("select[name='menu']").change(showAndHideFieldsByType);

$(document).ready(function() {
	var data = {
		ajax_cmd : "getContentTypes"
	};

	$.get("index.php", data, function(response, status) {
		AllTypes = response;
		showAndHideFieldsByType();
	});

});

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

	$("#btn-view-page").click(function() {
		var url = "../?goid=" + $("#page_id").val();
		// if page has unsaved changes open it in new window/tab
		// else open it in the same window/tab
		if (formchanged && !submitted) {
			window.open(url);
		} else {
			location.href = url;
		}
	})

	systemnameOrLanguageChanged($("input[name='system_title']"));
	systemnameOrLanguageChanged($("select[name='language']"));

	filterParentPages();
});
