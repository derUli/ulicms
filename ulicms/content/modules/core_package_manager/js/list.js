$(function() {
	bootbox.setDefaults({
		'locale' : $("html").data("select2-language")
	});

	// attach handler to form's submit event
	$('.uninstall-form').submit(function(event) {
		message = $(event.target).data("confirm-message");

		event.preventDefault();

		if (bootbox.confirm(message, function(result) {
			if (result) {
				var form = $(event.target);
				// submit the form
				$(form).ajaxSubmit({
					success : function(result) {
						// hide and remove the table row of the uninstalled
						// package
						$(form).closest("tr").fadeOut(400, function() {
							$(form).closest("tr").remove();
						});
					},
					error : function(xhr, status, error) {
						bootbox.alert(error);
					}
				});
			}
		}))
			;
	});
});
