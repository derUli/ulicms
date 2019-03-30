// Show preview image on change selection
$('input[name=youtube_embed_layout]').change(
	function() {
		var thumbnail = $('input[name=youtube_embed_layout]:checked').data(
			"thumbnail");
			$("img#preview").attr("src", thumbnail);
});