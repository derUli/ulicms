<p>
    <a
        href="<?php echo ModuleHelper::buildActionURL("design"); ?>"
        class="btn btn-default btn-back"><i class="fas fa-arrow-left"></i> <?php translate("back") ?></a>
</p>

<h1><?php translate("edit_footer_text"); ?></h1>
<?php echo ModuleHelper::buildMethodCallForm(FooterTextController::class, "save"); ?>
<p>
    <textarea name="footer_text" data-mimetype="text/html"
              class="<?php esc(get_html_editor()); ?>"><?php esc(Settings::get("footer_text")); ?></textarea>

</p>
<p>
    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>
        <?php translate("save"); ?></button>
</p>
<?php
echo ModuleHelper::endForm();
