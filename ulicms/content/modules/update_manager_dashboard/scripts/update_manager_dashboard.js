$(function() {
	$.post("index.php", {
		ajax_cmd : "anyUpdateAvailable"
	}, function(data, status) {
		if (data == "yes") {
			$("#update-manager-dashboard-container").slideDown();
		}
	});
});