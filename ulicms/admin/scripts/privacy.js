$(function() {
	$("select#language").change(
			function() {
				var url = "index.php?action=privacy_settings&language="
						+ $("select#language option:selected").val();
				location.replace(url);
			});
});