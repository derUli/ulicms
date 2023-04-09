<?php

use App\Constants\RequestMethod;
use App\Models\Content\Categories;

$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("banners")
        && $permissionChecker->hasPermission("banners_create")) {
    ?>

    <?php
    echo ModuleHelper::buildMethodCallForm(
        "BannerController",
        "create",
        [],
        RequestMethod::POST,
        [
            "autocomplete" => "off"
        ]
    );
    ?>
    <div class="field">
        <a href="<?php echo ModuleHelper::buildActionURL("banner"); ?>"
           class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i>
            <?php translate("back") ?></a>
    </div>

    <div class="field">
        <input type="radio" checked="checked" id="radio_gif" name="type"
               value="gif"
               onclick="$('#type_gif').slideDown();$('#type_html').slideUp();">
        <label
            for="radio_gif">
                <?php translate("gif_banner"); ?>
        </label>
    </div>
    <fieldset id="type_gif">
        <input type="hidden" name="add_banner" value="add_banner">
        <div class="field">
            <strong class="field-label">
                <?php translate("bannertext"); ?>
            </strong>
            <input type="text" name="banner_name" value="">
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate("IMAGE_URL"); ?></strong>
            <input type="text" name="image_url" value="">
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate("link_url"); ?>
            </strong>
            <input type="text" name="link_url" value="">
        </div>
    </fieldset>

    <div class="field">
        <input type="radio" id="radio_html" name="type" value="html"
               onclick="$('#type_html').slideDown();$('#type_gif').slideUp();">
        <label
            for="radio_html"><?php translate("html"); ?>
        </label>
    </div>

    <fieldset id="type_html" style="display: none">
        <div class="field">
            <textarea name="html" rows="10" cols="40"></textarea>
        </div>
    </fieldset>
    <div class="field">
        <strong class="field-label">
            <?php translate("enabled"); ?>
        </strong>
        <select
            name="enabled">
            <option value="1" selected><?php translate("yes"); ?></option>
            <option value="0"><?php translate("no"); ?></option>
        </select>
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate("date_from"); ?>
        </strong>
        <input
            type="text" class="datepicker" name="date_from" value="">
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate("date_to"); ?>
        </strong>
        <input type="text"
               class="datepicker" name="date_to" value="">
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate("language"); ?>
        </strong>
        <select name="language">
            <?php
            $languages = getAllLanguages();

    $languagesCount = count($languages);
    echo "<option value='all'>" . get_translation("every") . "</option>";
    for ($j = 0; $j < $languagesCount; $j++) {
        echo "<option value='" . $languages[$j] . "'>" .
        getLanguageNameByCode($languages[$j]) . "</option>";
    }
    ?>
        </select>
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate("category"); ?>
        </strong>
        <?php echo Categories::getHTMLSelect() ?></div>
    </div>

    <div class="voffset2">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i>
            <?php translate("save"); ?></button>
    </div>
    <?php
    echo ModuleHelper::endForm();
} else {
    noPerms();
}
