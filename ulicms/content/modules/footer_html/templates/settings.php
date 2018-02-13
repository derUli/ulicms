<?php
echo ModuleHelper::buildMethodCallForm ( "FooterHtml", "save" );
?>
<p><?php translate("footer_html_help");?></p>
<?php if(Request::getVar("save")){?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate("changes_was_saved")?>
		</div>
<?php }?>
<p>
	<textarea cols="25" rows="80" name="footer_html" class="codemirror"
		data-mimetype="text/html"><?php esc(Settings::get("footer_html"));?></textarea>
</p>
<p>
	<button type="submit" class="btn btn-primary"><?php translate("save");?></button>
</p>
<?php csrf_token_html();?>
<?php echo ModuleHelper::endForm();?>