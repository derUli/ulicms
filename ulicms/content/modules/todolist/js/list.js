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
	$(".btn-edit").click(function() {
		textTitle = $("span.title[data-id='" + $(this).data("id") + "']");

		var oldTitle = textTitle.text();
		var title = window.prompt("Title:", oldTitle);
		if (title && title != "") {
			$.ajax({
				url : $(this).data("url"),
				method : "POST",
				data : {
					"title" : title,
					"csrf_token" : $("input[name='csrf_token']").val(),
					"id" : $(this).data("id")
				},
				success : function(result) {
					textTitle.text(title);
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