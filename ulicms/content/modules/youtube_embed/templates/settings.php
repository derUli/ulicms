<?php
echo ModuleHelper::buildMethodCallForm("YoutubeEmbed", "settings");

$youtube_embed_layout = Settings::get("youtube_embed_layout");
if (! $youtube_embed_layout) {
    $youtube_embed_layout = "player";
}
?>
<?php if (Request::getVar("save")) { ?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate("changes_was_saved")?>
		</div>
<?php }?>
<label><?php translate("youtube_embed_layout");?></label>
<div class="radio">
	<label><input type="radio"
		data-thumbnail="<?php echo ModuleHelper::buildRessourcePath("youtube_embed", "images/player.jpg");?>"
		name="youtube_embed_layout"
		<?php if($youtube_embed_layout == "player") echo "checked";?>
		value="player"><?php translate("player");?></label>
</div>
<div class="radio">
	<label><input type="radio" name="youtube_embed_layout"
		value="thumbnail"
		data-thumbnail="<?php echo ModuleHelper::buildRessourcePath("youtube_embed", "images/thumbnail.jpg");?>"
		<?php if($youtube_embed_layout == "thumbnail") echo "checked";?>><?php translate("thumbnail");?></label>
</div>
<p>
	<img id="preview"
		src="<?php echo ModuleHelper::buildRessourcePath("youtube_embed", "images/{$youtube_embed_layout}.jpg");?>"
		alt="<?php translate("preview");?>?>" class="img-responsive">
</p>
<p>
	<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?php translate("save");?></button>
</p>
<?php echo ModuleHelper::endForm()?>
<?php
enqueueScriptFile(ModuleHelper::buildRessourcePath("youtube_embed", "js/settings.js"));
combinedScriptHtml();
?>