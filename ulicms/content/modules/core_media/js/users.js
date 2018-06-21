// Scripts for user account list page
var ajaxOptions = {
	success : function(responseText, statusText, xhr, $form) {
		var action = $($form).attr("action");
		var id = url('?admin', action);
		var list_item_id = "dataset-" + id
		var tr = $("tr#" + list_item_id);
		$(tr).fadeOut();

	}

}

$("form.delete-form").ajaxForm(ajaxOptions);