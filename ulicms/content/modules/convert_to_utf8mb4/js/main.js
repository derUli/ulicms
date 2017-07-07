String.prototype.contains = function(it) {
	return this.indexOf(it) != -1;
};

$(function() {
	$("#btn_start_conversion").click(function() {
		convertNextTable();
	});
});

function convertNextTable() {
	var url = $("#url").data("action-url");
	$.get(url, function(text, status) {
		$("#convert-container").html(text);
		if (!text.contains("<!--finish-->")) {
			convertNextTable();
		} else {
			location.replace($("#url").data("finish-url"));
		}
	});
}