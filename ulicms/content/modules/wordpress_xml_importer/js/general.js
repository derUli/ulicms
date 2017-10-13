$(function() {
	$("input[name='file']").on("change", function() {
		if ($(this).val().length > 0) {
			$("#import-to").slideDown();
		} else {
			$("#import-to").slideUp();
		}
	});
	$("select[name='import_to']").on("change", function() {

		if ($(this).val() == "blog") {
			$("#default-category").slideUp();
		} else {
			$("#default-category").slideDown();

		}
	});
	$("select[name='language']").change(function() {
		filterParentPages();
	});

	$("select[name='menu']").change(function() {
		filterParentPages();
	});
	$("input[name='file']").trigger("change");
	$("select[name='import_to']").trigger("change");
	filterParentPages();
});

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