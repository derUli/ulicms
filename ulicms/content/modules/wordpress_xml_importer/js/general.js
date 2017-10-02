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
	$("input[name='file']").trigger("change");
	$("select[name='import_to']").trigger("change");
});