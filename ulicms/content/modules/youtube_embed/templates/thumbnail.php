<?php
$videoId = ViewBag::get("video_id");
$youtubeUrl = "https://www.youtube.com/watch?v={$videoId}";
$actionURL = ModuleHelper::buildMethodCallUrl("YoutubeEmbed", "thumbnail", "url=" . urlencode($youtubeUrl));
?>
<a href="<?php esc($youtubeUrl);?>"
	title="<?php translate("watch_on_youtube");?>" target="_blank"> <img
	src="<?php esc($actionURL);?>"
	alt="<?php translate("watch_on_youtube");?>"></a>
