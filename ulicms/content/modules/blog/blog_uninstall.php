<?php
$rss_file = ULICMS_ROOT."/blog_rss.php";
if (file_exists ( $rss_file ))
	@unlink ( $rss_file );
?>