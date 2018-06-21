// Script for list of video media
$(function() {
	$('#category').on(
			'change',
			function(e) {
				var valueSelected = $('#category').val();
				location.replace("index.php?action=videos&filter_category="
						+ valueSelected)
			});
	var ajaxOptions = {
		success : function(responseText, statusText, xhr, $form) {
			var action = $($form).attr("action");
			var id = url('?delete', action);
			var list_item_id = "dataset-" + id
			var tr = $("tr#" + list_item_id);
			$(tr).fadeOut();
		}
	}

	$("form.delete-form").ajaxForm(ajaxOptions);
});