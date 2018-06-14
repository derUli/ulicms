// Script for the "privacy settings" page
$(function() {
	// Change language
	// Privacy settings are language specific
	$("select#language").change(
			function() {
				var url = "index.php?action=privacy_settings&language="
						+ $("select#language option:selected").val();
				location.replace(url);
			});
	// expand privacy policy checkbox options when enabled
	$("#privacy_policy_checkbox_enable").change(function(e) {
		var checked = $(this).is(":checked");
		if (checked) {
			$("#privacy_policy_checkbox_text_container").slideDown();
		} else {
			$("#privacy_policy_checkbox_text_container").slideUp();
		}
	});

});