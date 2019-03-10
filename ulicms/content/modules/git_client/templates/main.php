<?php if (ViewBag::get("has_changes")) {
    ?>
    <div class="alert alert-warning">
        <?php translate("there_are_local_changes"); ?>
    </div>
    <?php
} else {
    ?>
    <div class = "alert alert-success">
        <?php translate("no_changes"); ?>
    </div>
    <?php
}
?>
<a href="#" data-url="<?php echo ModuleHelper::buildMethodCallUrl(GitClient::class, "pull"); ?>" class="btn btn-primary remote-alert"><?php translate("git_pull"); ?></a>

<?php
enqueueScriptFile(ModuleHelper::buildRessourcePath(GitClient::MODULE_NAME, " js/main.js"));
combinedScriptHtml();
?>