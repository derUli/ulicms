$(function() {
	$("*[data-sql]").click(
			function(event) {
				event.preventDefault();
				var sql = $(this).data("sql");
				$("#sql_code").next('.CodeMirror').get(0).CodeMirror.getDoc()
						.setValue(sql);
			});

	$("#btn-execute").click(function(event) {
		event.preventDefault()
		$("#result-spinner").show();
		$("#result-data").hide();
		var form = $(this).closest("form");
		var url = form.attr("action");
		$.ajax({
			type : "POST",
			url : url,
			data : form.serialize(), // serializes the form's elements.
			success : function(response) {
				$("#result-data").html(response).slideDown();
			},
			error : function(jqXHR, exception) {
				if (jqXHR.status === 0) {
					bootbox.alert('Not connect.\n Verify Network.');
				} else if (jqXHR.status == 404) {
					bootbox.alert('Requested page not found. [404]');
				} else if (jqXHR.status == 500) {
					bootbox.alert('Internal Server Error [500].');
				} else if (exception === 'parsererror') {
					bootbox.alert('Requested JSON parse failed.');
				} else if (exception === 'timeout') {
					bootbox.alert('Time out error.');
				} else if (exception === 'abort') {
					bootbox.alert('Ajax request aborted.');
				} else {
					bootbox.alert('Uncaught Error.\n' + jqXHR.responseText);
				}
			},
			complete : function(jqXHR, textStatus) {
				$("#result-spinner").hide();
			}
		});
	});
});
