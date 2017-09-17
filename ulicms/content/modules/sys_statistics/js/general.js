$(function() {
	$("#sys_statistics_language select").change(function() {
		$(this).closest("form").submit();
	})
});
