<?php 
if(containsModule(get_requested_pagename(), "lightbox_gallery")) {
?>
<link type="text/css" href="<?php echo getModulePath("lightbox_gallery");?>lightbox/css/lightbox.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo getModulePath("lightbox_gallery");?>lightbox/js/lightbox.js"></script>

<?php 
}
?>
