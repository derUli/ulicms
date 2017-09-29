jQuery.fn.swapWith = function(to) {
	return this.each(function() {
		var copy_to = $(to).clone(true);
		var copy_from = $(this).clone(true);
		$(to).replaceWith(copy_from);
		$(this).replaceWith(copy_to);
	});
};

$(function(e) {
	$("#btn-new").click(function() {
		var title = window.prompt(Translation.TITLE + ":", "");
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
		var title = window.prompt(Translation.TITLE + ":", oldTitle);
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
	$(".btn-delete").click(function() {
		element = $(this);
		$.ajax({
			url : $(this).data("url"),
			method : "POST",
			data : {
				"id" : $(this).data("id"),
				"csrf_token" : $("input[name='csrf_token']").val()
			},
			success : function(result) {
				$(element).closest("td").closest("tr").remove();
			}
		});
	});
	$(".btn-up").click(function() {
		element = $(this);
		$.ajax({
			url : $(this).data("url"),
			method : "POST",
			data : {
				"id" : $(this).data("id"),
				"csrf_token" : $("input[name='csrf_token']").val()
			},
			success : function(result) {
				var firstTr = $(element).closest("td").closest("tr")
				var otherTr = firstTr.prev("tr");
				if (firstTr.length && otherTr.length) {
					$(firstTr).swapWith(otherTr);
				}
			}
		});
	});
	$(".btn-down").click(function() {
		element = $(this);
		$.ajax({
			url : $(this).data("url"),
			method : "POST",
			data : {
				"id" : $(this).data("id"),
				"csrf_token" : $("input[name='csrf_token']").val()
			},
			success : function(result) {
				var firstTr = $(element).closest("td").closest("tr");
				var otherTr = $(firstTr).next("tr");
				if (firstTr.length && otherTr.length) {
					$(firstTr).swapWith(otherTr);
				}
			}
		});
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