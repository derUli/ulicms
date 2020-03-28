<?php
csrf_token_html();
// The filter features are disabled in this release
// $show_filters = Settings::get("user/" . get_user_id() . "/show_filters");
$show_filters = false;
?>
<div
    class="filter-wrapper has-not-allowed"
    data-parent-pages-url="<?php
    echo
    ModuleHelper::buildMethodCallUrl(
            PageController::class,
            "getParentSelection",
            "no_id=1"
    );
    ?>"
    >
    <div class="checkbox">
        <label><input type="checkbox" class="js-switch" name="show_filters" id="show_filters"
                      value="1" data-url="<?php echo ModuleHelper::buildMethodCallUrl(PageController::class, "toggleFilters"); ?>"
                      disabled
                      <?php if ($show_filters) echo "checked"; ?>>
            <span class="js-switch-label">
                <?php translate("show_filters"); ?>
            </span>
        </label>
    </div>

    <div class="filters"  style="<?php
    if (!$show_filters)
        echo "display:none";
    ?>">
        <div class="row">
            <div class="col-xs-6">
                <?php
                echo Template::executeModuleTemplate(
                        "core_content",
                        "pages/partials/filters/languages.php"
                );
                ?>

            </div>
            <div class="col-xs-6">
                <?php
                echo Template::executeModuleTemplate(
                        "core_content",
                        "pages/partials/filters/types.php"
                );
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <?php
                echo Template::executeModuleTemplate(
                        "core_content",
                        "pages/partials/filters/categories.php"
                );
                ?>
            </div>
            <div class="col-xs-6">
                <?php
                echo Template::executeModuleTemplate(
                        "core_content",
                        "pages/partials/filters/menus.php"
                );
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <?php
                echo Template::executeModuleTemplate(
                        "core_content",
                        "pages/partials/filters/parent.php"
                );
                ?>
            </div>
            <div class="col-xs-6">
                <?php
                echo Template::executeModuleTemplate(
                        "core_content",
                        "pages/partials/filters/active.php"
                );
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <?php
                echo Template::executeModuleTemplate(
                        "core_content",
                        "pages/partials/filters/approved.php"
                );
                ?>
            </div>
        </div>
    </div>
</div>
