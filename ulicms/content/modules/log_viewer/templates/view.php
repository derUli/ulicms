<?php
$path = basename(dirname(ViewBag::get("file"))) . "/" . basename(ViewBag::get("file"));
$anchor = "dir-" . md5(basename(dirname(ViewBag::get("file"))));
?>
<a
	href="<?php esc(ModuleHelper::buildAdminURL("log_viewer"));?>#<?php esc($anchor)?>"
	class="btn btn-default"><?php translate("back")?></a>
<h4><?php esc($path);?></h4>
<textarea cols="80" rows=20 readonly>
<?php esc(trim(File::read(ViewBag::get("file"))));?>
</textarea>