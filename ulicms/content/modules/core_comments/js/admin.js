$(function() {
	$(".ajax-alert").click(function(event) {
		event.preventDefault();
		var url = $(event.target).data("url");
		$.ajax({
			url : url,
			success : function(result) {
				bootbox.alert(result);
			}
		});
	});
});