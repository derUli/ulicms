<?php 
if(!$acl->hasPermission("install_packages")){
noperms();
}else{
     ?>
<h1>Verfügbare Pakete</h1>
<noscript>
<p>Bitte aktivieren Sie Javascript!</p>
</noscript>
<div id="loadpkg">
<img style="margin-right:15px;float:left;" src="gfx/loading.gif" alt="Bitte warten..."> <div style="padding-top:3px;">Daten werden geladen...</div>
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

<?php }
?>