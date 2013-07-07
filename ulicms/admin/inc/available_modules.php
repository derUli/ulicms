<?php if(!is_admin()){?>
<p>Zugriff verweigert</p>
<?php } else {
?>
<h1>Verfügbare Pakete</h1>
<div id="pkglist">
<img style="margin-right:15px;float:left;" src="gfx/loading.gif" alt="Bitte warten..."> <div style="padding-top:3px;">Daten warten geladen...</div>
</div>

<script type="text/javascript">
$(window).load(function(){

$.get("index.php?ajax_cmd=available_modules", function(result){
   $("div#pkglist").html(result);
});

});
</script>

<?php }?>