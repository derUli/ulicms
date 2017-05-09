$(function() {
	$("select#language").change(
			function() {
				var url = "index.php?action=motd&language="
						+ $("select#language option:selected").val();
				location.replace(url);
			});

});