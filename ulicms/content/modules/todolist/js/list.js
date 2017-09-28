$(function(e) {
	$("#btn-new").click(function() {
		var title = window.prompt("Title:", "");
		if (title && title != "") {
			$.ajax({
				url : $(this).data("url"),

				method : "POST",
				data : {
					"title" : title,
					"csrf_token" : $("input[name='csrf_token']").val()
				},
				success : function(result) {
					$("table#todolist tbody").append(result);
				}
			});
		}
	});
	$(".checkbox-done").change(function() {
		var isChecked = $(this).is(":checked") ? 1 : 0;
		element = $(this);
		$.ajax({
			url : $(this).data("url"),
			method : "POST",
			data : {
				"id" : $(this).data("id"),
				"done" : isChecked,
				"csrf_token" : $("input[name='csrf_token']").val()
			},
			success : function(result) {
				// do nothing
			}
		});
	});
});