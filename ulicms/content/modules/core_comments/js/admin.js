$(function() {
	// Show full text in an alert modal when
	// the user clicks on the shortened text
	$(".ajax-alert").click(function(event) {
		event.preventDefault();
		var url = $(event.target).data("url");
		// do an ajax call
		$.ajax({
			url : url,
			success : function(result) {
				// show the response to the user in an bootbox alert
				bootbox.alert({
					message : result,
					size : "large"
				});
			}
		});
	});
});