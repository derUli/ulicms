$(function() {
	$.post("index.php", {
		ajax_cmd : "ajax_patch_check"
	}, function(data, status) {
		if (data.length > 0) {
			$("#patch-notification #patch-message").html(data);
			$("#patch-notification").slideDown();
		}
	});

	$.post("index.php", {
		ajax_cmd : "core_update_check"
	}, function(data, status) {
		if (data.length > 0) {
			$("#core-update-check #core-update-message").html(data);
			$("#core-update-check").slideDown();
		}
	});
});