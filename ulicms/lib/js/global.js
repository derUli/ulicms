function askForDelete() {
	return confirm(Translation.ASK_FOR_DELETE);
}

$(function() {
	$("form.delete-form").submit(function() {
		return askForDelete();
	});
	$('textarea.codemirror').each(function(index, elem) {
		var mode = "text/html";
		if ($(elem).data("mimetype")) {
			mode = $(elem).data("mimetype");
		}
		CodeMirror.fromTextArea(elem, {
			lineNumbers : true,
			matchBrackets : true,
			mode : mode,
			indentUnit : 0,
			indentWithTabs : false,
			enterMode : "keep",
			tabMode : "shift",
			readOnly : $(elem).prop("readonly")
		});
	});
})
