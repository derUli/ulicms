$(document).ready(function() {
	$("select").select2();
});

$("select#language").change(function() {
	var language = $("select#language").val();
	window.location.replace("index.php?step=1&language=" + language)

})