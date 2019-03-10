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
<?php
echo ModuleHelper::buildMethodCallForm(GitClient::class, "checkoutBranch",
        array(), RequestMethod::POST, array("id" => "checkout_branch_form"));
?>
<div class="form-group">
    <div class="row">
        <div class="col-xs-6 col-md-9 col-lg-10">
            <?php
            echo UliCMS\HTML\Input::SingleSelect("name", ViewBag::get("branch"), ViewBag::get("branches"));
            ?>
        </div>
        <div class="col-xs-6 col-md-3 text-right col-lg-2">
            <button type="submit" class="btn btn-default"><i class="fas fa-code-branch"></i> <?php translate("checkout_branch");
            ?></button>
        </div>
    </div>
</div>

<?php echo ModuleHelper::endForm(); ?>
<div class="btn-group form-group">

    <a href="#" data-url="<?php echo ModuleHelper::buildMethodCallUrl(GitClient::class, "pull"); ?>" class="btn btn-default remote-alert">
        <i class="fas fa-arrow-down"></i>
        <?php translate("git_pull"); ?></a>
    <a href="<?php echo ModuleHelper::buildMethodCallUrl(GitClient::class, "fetch"); ?>" class="btn btn-default">
        <i class="fas fa-arrow-right"></i>
        <?php translate("git_fetch"); ?></a>

    <a data-url="<?php echo ModuleHelper::buildMethodCallUrl(GitClient::class, "mergeBranch"); ?>" id="btn-merge" class="btn btn-default">
        <i class="fas fa-code-branch"></i>
        <?php translate("git_merge"); ?></a>

</div>
<?php echo ModuleHelper::buildMethodCallForm(GitClient::class, "commitAndPush"); ?>
<div class="form-group">
    <label for="comment"><?php
        translate("commit_message");
        ?>
    </label>

    <?php
    echo UliCMS\HTML\Input::TextArea("message", "", 3, 80, array(
        "required" => "required",
        "placeholder" => get_translation("commit_message")
    ));
    ?>
</div>
<div class="form-group">
    <button type="submit" class="btn btn-primary" <?php if (!ViewBag::get("has_changes")) echo "disabled"; ?>>
        <i class="fas fa-arrow-up"></i>
        <?php translate("git_commit_and_push");
        ?></button>
</div>
<?php echo ModuleHelper::endForm(); ?>
<?php echo ModuleHelper::buildMethodCallForm(GitClient::class, "createBranch"); ?>
<div class="form-group">
    <div class="row">
        <div class="col-xs-6 col-md-9 col-lg-10">
            <?php
            echo UliCMS\HTML\Input::TextBox("name", "", "text", array(
                "placeholder" => get_translation("branch_name"),
                "required" => "required"
            ));
            ?>
        </div>
        <div class="col-xs-6 col-md-3 col-lg-2 text-right">
            <button type="submit" class="btn btn-default"><i class="fas fa-code-branch"></i> <?php translate("git_new_branch");
            ?></button>
        </div>
    </div>
</div>
<?php
echo ModuleHelper::endForm();
?>

<?php
$translation = new JSTranslation();
$translation->addKey("git_merge");
$translation->addKey("merge_branch");
$translation->addKey("select_branch");
$translation->addKey("cancel");
$translation->render();

enqueueScriptFile(ModuleHelper::buildRessourcePath(GitClient::MODULE_NAME, "js/main.js"));
combinedScriptHtml();
