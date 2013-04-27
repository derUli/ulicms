<?php 
if(is_logged_in()){
?>
<script type="text/javascript" src="admin/ckeditor/ckeditor.js"></script>
<?php }?>
<link rel="alternate" type="application/rss+xml" title="Blog Newsfeed" href="blog_rss.php?s=<?php echo get_requested_pagename()?>&lang=<?php echo $_SESSION["language"]?>" />
