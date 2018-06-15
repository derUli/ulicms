$(function() {
	$("#settings_simple")
			.ajaxForm(
					{
						beforeSubmit : function(e) {
							$("#message").html("");
							$("#loading").show();
						},
						success : function(e) {
							$("#loading").hide();
							$("#message")
									.html(
											"<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
						}
					});
});