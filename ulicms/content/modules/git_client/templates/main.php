<?php if (ViewBag::get("has_changes")) {
    ?>
    <div class="alert alert-warning" id="alert-changes" data-has-changes="true"
         data-url="<?php echo ModuleHelper::buildMethodCallUrl(GitClient::class, "checkForChanges"); ?>">
             <?php translate("there_are_local_changes"); ?>
    </div>
    <?php
} else {
    ?>
    <div class="alert alert-success" id="alert-changes" data-has-changes="false"
         data-url="<?php echo ModuleHelper::buildMethodCallUrl(GitClient::class, "checkForChanges"); ?>">
             <?php translate("no_changes"); ?>
    </div>
    <?php
}
?>
<div class="form-group"><a href="#" data-url="<?php echo ModuleHelper::buildMethodCallUrl(GitClient::class, "pull"); ?>" class="btn btn-default remote-alert">
        <i class="fas fa-arrow-down"></i>
        <?php translate("git_pull"); ?></a>
</div>
<?php echo ModuleHelper::buildMethodCallForm(GitClient::class, "commitAndPush"); ?>
<div class="form-group">
    <label for="comment"><?php
        translate("commit_message");
        ?>
    </label>

    <?php
    echo UliCMS\HTML\Input::TextArea("message", "", 3, 80, array(
        "required" => "required"
    ));
    ?>
</div>
<button type="submit" class="btn btn-primary" <?php if (!ViewBag::get("has_changes")) echo "disabled"; ?>>
    <i class="fas fa-arrow-up"></i>
    <?php translate("git_commit_and_push");
    ?></button>
<?php echo ModuleHelper::endForm(); ?>
<?php
enqueueScriptFile(ModuleHelper::buildRessourcePath(GitClient::MODULE_NAME, "js/main.js"));
combinedScriptHtml();
