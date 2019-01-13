$(function() {
	$("#select-all-receivers").click(function() {
		$("#receivers option").prop('selected', true);
		$("#receivers").trigger("change");
	});
	$("#select-nothing-receivers").click(function() {
		$("#receivers option").prop('selected', false);
		$("#receivers").trigger("change");
	});
});
