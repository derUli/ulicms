<?php

use App\Constants\RequestMethod;
use App\Exceptions\DatasetNotFoundException;
use App\HTML\Alert;
use App\Models\Content\Advertisement\Banner;
use App\Models\Content\Categories;

$permissionChecker = new \App\Security\ACL();
if ($permissionChecker->hasPermission('banners')
        && $permissionChecker->hasPermission('banners_edit')) {
    $banner = Request::getVar('banner', 0, 'int');
    $row = new Banner();
    try {
        $row->loadByID($banner);
    } catch (DatasetNotFoundException $e) {
        $row = null;
    }
    if ($row) {
        ?>
        <div class="field">
            <a href="<?php echo ModuleHelper::buildActionURL('banner'); ?>"
               class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i>
                <?php translate('back'); ?></a>
        </div>
        <?php
        echo ModuleHelper::buildMethodCallForm(
            'BannerController',
            'update',
            [],
            RequestMethod::POST,
            [
                'autocomplete' => 'off'
            ]
        );
        ?>
        <h4><?php translate('preview'); ?></h4>
        <?php
        if ($row->getType() == 'gif') {
            ?>
            <div class="field">
                <a
                    href="<?php Template::escape($row->getLinkUrl()); ?>"
                    target="_blank"><img
                        src="<?php Template::escape($row->getImageUrl()); ?>"
                        title="<?php Template::escape($row->getName()); ?>"
                        alt="<?php Template::escape($row->getName()); ?>"
                        border=0> </a>
            </div>
            <?php
        } else {
            echo $row->getHtml();
        }
        ?>
        <input type="hidden" name="edit_banner" value="edit_banner">
        <input type="hidden" name="id" value="<?php echo $row->getId(); ?>">
        <div class="field">
            <input type="radio"
            <?php
            if ($row->getType() == 'gif') {
                echo 'checked="checked"';
            }
        ?>
                   id="radio_gif" name="type" value="gif"
                   onclick="$('#type_gif').slideDown();
                                   $('#type_html').slideUp();">
            <label
                for="radio_gif"><?php translate('gif_banner'); ?></label>
        </div>
        <fieldset id="type_gif" style="<?php
        if ($row->getType() !== 'gif') {
            echo 'display:none';
        }
        ?>">
            <div class="field">
                <strong class="field-label">
                    <?php translate('bannertext'); ?>
                </strong>
                <input type="text" name="banner_name"
                       value="<?php Template::escape($row->getName()); ?>">
            </div>
            <div class="field">
                <strong class="field-label">
                    <?php translate('IMAGE_URL'); ?>
                </strong>
                <input type="text" name="image_url"
                       value="<?php Template::escape($row->getImageUrl()); ?>">
            </div>
            <div class="field">
                <strong class="field-label"><?php translate('link_url'); ?></strong>

                <input type="text" name="link_url"
                       value="<?php Template::escape($row->getLinkUrl()); ?>">
            </div>
        </fieldset>
        <div class="field">
            <input type="radio"
            <?php
            if ($row->getType() == 'html') {
                echo 'checked="checked"';
            }
        ?>
                   id="radio_html" name="type" value="html"
                   onclick="$('#type_html').slideDown();$('#type_gif').slideUp();">
            <label for="radio_html">HTML</label>
        </div>
        <fieldset id="type_html" style="<?php
        if ($row->getType() !== 'html') {
            echo 'display:none';
        }
        ?>">
            <div class="field">
                <textarea name="html" cols=40 rows=10><?php esc($row->getHtml()); ?></textarea>
            </div>
        </fieldset>


        <div class="field">
            <strong class="field-label"><?php translate('enabled'); ?></strong> <select
                name="enabled">
                <option value="1"
                <?php
                if ($row->getEnabled()) {
                    echo 'selected';
                }
        ?>>
                    <?php translate('yes'); ?></option>
                <option value="0"
                <?php
        if (! $row->getEnabled()) {
            echo 'selected';
        }
        ?>>
                    <?php translate('no'); ?></option>
            </select>
        </div>
        <div class="field">
            <strong class="field-label"><?php translate('date_from'); ?></strong> <input
                type="text" class="datepicker" name="date_from"
                value="<?php esc($row->getDateFrom()); ?>">
        </div>
        <div class="field">
            <strong class="field-label"><?php translate('date_to'); ?></strong>
            <input
                type="text"
                class="datepicker"
                name="date_to"
                value="<?php esc($row->getDateTo()); ?>">
        </div>
        <div class="field">
            <strong class="field-label"><?php translate('language'); ?></strong>

            <select name="language">
                <?php
        $languages = getAllLanguages();
        $page_language = $row->getLanguage();

        if ($page_language === 'all') {
            echo "<option value='all' selected='selected'>" .
            get_translation('every') . '</option>';
        } else {
            echo "<option value='all'>" . get_translation('every') . '</option>';
        }

        $languagesCount = count($languages);

        for ($j = 0; $j < $languagesCount; $j++) {
            if ($languages[$j] === $page_language) {
                echo "<option value='" . $languages[$j] . "' selected>" .
                getLanguageNameByCode($languages[$j]) . '</option>';
            } else {
                echo "<option value='" . $languages[$j] . "'>" .
                getLanguageNameByCode($languages[$j]) . '</option>';
            }
        }

        $pages = getAllPages($page_language, 'title');
        ?>
            </select>
        </div>
        <div class="field">
            <strong class="field-label"><?php translate('category'); ?></strong>

            <?php echo Categories::getHTMLSelect($row->getCategoryId()); ?>
        </div>
        <div class="voffset2">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i>
                <?php translate('save'); ?></button>
        </div>
        </form>
        <?php
    } else {
        echo Alert::danger(
            get_translation('not_found')
        );
    }
} else {
    noPerms();
}
