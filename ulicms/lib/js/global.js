$(function() {
	$("form.delete-form").submit(function() {
		return confirm(Translation.ASK_FOR_DELETE);
	});
})