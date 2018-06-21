// This the script for the categories list page
$(function() {
	var ajaxOptions = {
		success : function(responseText, statusText, xhr, $form) {
			var action = $($form).attr("action");
			var id = url('?del', action);
			var list_item_id = "dataset-" + id
			var tr = $("tr#" + list_item_id);
			$(tr).fadeOut();
		}
	}

	$("form.delete-form").ajaxForm(ajaxOptions);
});