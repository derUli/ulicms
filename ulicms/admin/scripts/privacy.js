$(function() {
	$("select#language").change(
			function() {
				var url = "index.php?action=privacy_settings&language="
						+ $("select#language option:selected").val();
				location.replace(url);
			});
	$("#privacy_policy_checkbox_enable").change(function(e)
	{
		var checked = $(this).is(":checked");
		if (checked) {
			$("#privacy_policy_checkbox_text_container").slideDown();
		} else {
			$("#privacy_policy_checkbox_text_container").slideUp();
		}
	});
	
});