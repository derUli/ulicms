// scripts for meta keywords settings page
$(function() {
	$("#meta_keywords_settings")
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