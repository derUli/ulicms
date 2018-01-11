<?php
if (! $acl->hasPermission ( "install_packages" )) {
	noperms ();
} else {
	?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("install_method");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate("available_packages")?></h1>
<div id="loadpkg">
	<img style="margin-right: 15px; float: left;" src="gfx/loading.gif"
		alt="Bitte warten...">
	<div style="padding-top: 3px;">
	<?php translate("loading_data"); ?>
	</div>
</div>
<div id="pkglist"></div>
<script type="text/javascript">
$(window).load(function(){
	$("div#loadpkg").slideDown();
	$.get("index.php?ajax_cmd=available_modules", function(result){
		$("div#loadpkg").slideUp();
		$("div#pkglist").html(result);
		$("div#pkglist").slideDown();
	});
});
</script>
<?php }?>