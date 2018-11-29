$(function() {
	var url = $("#ulicms-feed").data("url");
	$('#ulicms-feed').load(url);
	var url = $("#patch-notification").data("url");
	$.get(url, function(data, status) {
		if (data.length > 0) {
			$("#patch-notification #patch-message").html(data);
			$("#patch-notification").slideDown();
		}
	});

	$("#show_positions").change(function(event) {
		var url = $(event.target).data("url");
		$.ajax({
			method : "get",
			url : url,
			error : function(xhr, status, error) {
				alert(xhr.responseText);
			}
		});

	});
});