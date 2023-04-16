<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\HTML\Input;
use App\HTML\ListItem;
use App\Translations\JSTranslation;

$skins = BackendHelper::getCKEditorSkins();
$skinItems = [];
foreach ($skins as $skin) {
    $skinItems[] = new ListItem($skin, $skin);
}

$controller = ControllerRegistry::get();
$permissionChecker = new \App\Security\Permissions\ACL();
if (! $permissionChecker->hasPermission('design')) {
    noPerms();
} else {
    $allThemes = getAllThemes();
    $fonts = $controller->getFontFamilys();
    $theme = Settings::get('theme');
    $mobile_theme = Settings::get('mobile_theme');
    $default_font = Settings::get('default_font');
    $title_format = Settings::get('title_format');
    $font_size = Settings::get('font_size');
    $ckeditor_skin = Settings::get('ckeditor_skin');
    $font_sizes = $controller->getFontSizes();
    $no_mobile_design_on_tablet = Settings::get('no_mobile_design_on_tablet');
    $modManager = new ModuleManager();
    ?>
    <p>
        <a
            href="<?php echo ModuleHelper::buildActionURL('settings_categories'); ?>"
            class="btn btn-default btn-back is-not-ajax"><i class="fas fa-arrow-left"></i> <?php translate('back'); ?></a>
    </p>
    <h1>
        <?php translate('design'); ?>
    </h1>
    <?php
    echo ModuleHelper::buildMethodCallForm('DesignSettingsController', 'save', [], 'post', [
        'id' => 'designForm'
    ]);
    ?>
    <div class="scroll">
        <table style="width: 100%;">
            <tr>
                <td style="width: 300px;"><strong><?php translate('title_format'); ?> </strong></td>
                <td><input type="text" name="title_format"
                           value="<?php esc($title_format); ?>"></td>
            </tr>
            <tr>
                <td><strong><?php translate('frontend_design'); ?> </strong></td>
                <td><select name="theme" size="1"
                            data-preview-target-element="#theme-preview">
                                <?php
         foreach ($allThemes as $th) {
             ?>
                            <option value="<?php echo $th; ?>"
                            <?php
                            if ($th === $theme) {
                                echo ' selected';
                            }
             ?>
                                    data-preview-url="<?php
                     echo ModuleHelper::buildMethodCallUrl(
                         DesignSettingsController::class,
                         'themePreview',
                         "theme={$th}"
                     );
             ?>"
                                    >
                                        <?php echo $th; ?>
                            </option>
                        <?php }
         ?>
                    </select>
                    <div id="theme-preview" class="voffset3">
                        <i class="fa fa-spinner fa-spin"></i>
                        <div class="preview"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td><strong><?php translate('mobile_design'); ?> </strong></td>
                <td>
                    <p>
                        <select name="mobile_theme" size="1"
                                data-preview-target-element="#theme-mobile-preview">
                            <option value=""
                            <?php
             if (! $mobile_theme) {
                 echo ' selected';
             }
    ?>
                                    >
                                [
                                <?php translate('standard'); ?>
                                ]
                            </option>
                            <?php
    foreach ($allThemes as $th) {
        ?>
                                <option value="<?php echo $th; ?>"
                                <?php
        if ($th === $mobile_theme) {
            echo ' selected';
        }
        ?>
                                        data-preview-url="<?php
                echo ModuleHelper::buildMethodCallUrl(
                    DesignSettingsController::class,
                    'themePreview',
                    "theme={$th}"
                );
        ?>">
                                            <?php echo $th; ?>
                                </option>
                            <?php }
    ?>
                        </select>
                    <div id="theme-mobile-preview" class="voffset3">
                        <i class="fa fa-spinner fa-spin"></i>
                        <div class="preview"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td><strong><?php translate('no_mobile_design_on_tablet'); ?> </strong></td>
                <td><input type="checkbox" name="no_mobile_design_on_tablet"
                           class="js-switch"

                           <?php
                           if ($no_mobile_design_on_tablet) {
                               echo ' checked';
                           }
    ?>></td>
            </tr>
            <tr>
                <td><strong><?php translate('editor_skin'); ?> </strong></td>
                <td>
                    <?php
                    echo Input::singleSelect(
                        'ckeditor_skin',
                        $ckeditor_skin,
                        $skinItems
                    );
    ?>
                </td>

            </tr>
            <tr>
                <td><strong><?php translate('font_family'); ?> </strong></td>
                <td><select name="default_font" id="default_font" size=1>
                        <?php
        $font_amount = count($fonts);
    $i = 1;
    foreach ($fonts as $key => $value) {
        $selected = '';
        if ($default_font === $value) {
            $selected = 'selected';
        }

        if (! in_array($default_font, $fonts) && $i === $font_amount) {
            $selected = 'selected';
        }

        echo '<optgroup>';

        echo "<option value=\"{$value}\" {$selected}>{$key}</option>";
        echo '</optgroup>';

        $i++;
    }
    ?></select>
                </td>
            </tr>
            <tr>
                <td><strong><?php translate('font_size'); ?> </strong>

                <td> <select name="font_size" id="font_size">
                        <?php
    foreach ($font_sizes as $size) {
        echo '<option value="' . $size . '"';
        if ($font_size == $size) {
            echo ' selected';
        }
        echo '>';
        echo $size;
        echo '</option>';
    }
    ?>
                    </select></td>
            </tr>
            <tr id="font-preview">
                <td></td>
                <td>Franz jagt im komplett verwahrlosten Taxi quer durch Bayern</td>
            </tr>
            <tr>
                <td><strong><?php translate('HEADER_BACKGROUNDCOLOR'); ?> </strong></td>
                <td><input name="header_background_color"
                           class="jscolor {
                               hash:true,caps:true
                           }"
                           value="<?php echo _esc(Settings::get('header_background_color')); ?>"></td>
            </tr>
            <tr>
                <td><strong><?php translate('font_color'); ?> </strong></td>
                <td><input name="body_text_color"
                           class="jscolor {
                               hash:true,caps:true
                           }"
                           value="<?php echo _esc(Settings::get('body_text_color')); ?>"></td>
            </tr>
            <tr>
                <td><strong><?php translate('BACKGROUNDCOLOR'); ?> </strong></td>
                <td><input name="body_background_color"
                           class="jscolor {
                               hash:true,caps:true
                           }"
                           value ="<?php echo _esc(Settings::get('body_background_color')); ?>"></td>
            </tr>
            <?php
            if ($permissionChecker->hasPermission('logo')) {
                ?>
                <tr>
                    <td>
                        <strong><?php translate('logo'); ?></strong>
                    </td>
                    <td>

                        <a href="index.php?action=logo" class="btn btn-default is-not-ajax"><i
                                class="fas fa-tools"></i> <?php translate('upload_new_logo'); ?></a>
                    </td></tr>
            <?php }
            ?>
            <?php
            if ($permissionChecker->hasPermission('favicon')) {
                ?>
                <tr>
                    <td><strong><?php translate('favicon'); ?></strong></td>
                    <td><a href="index.php?action=favicon" class="btn btn-default is-not-ajax"><i
                                class="fas fa-file-image"></i> <?php translate('upload_new_favicon'); ?></a>
                    </td>
                </tr>
            <?php }
            ?>
            <?php
            if ($permissionChecker->hasPermission('footer_text')) {
                ?>
                <tr>
                    <td><strong><?php translate('footer'); ?></strong></td>
                    <td><a href="index.php?action=footer_text" class="btn btn-default is-not-ajax"><i class="fas fa-edit"></i> <?php translate('edit_footer_text'); ?></a>
                    </td>
                </tr>
            <?php }
            ?>
        </table>
    </div>
    <p class="voffset3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?php translate('save_changes'); ?></button>
    </p>
    <?php
    echo ModuleHelper::endForm();
    $translation = new JSTranslation();
    $translation->addKey('changes_was_saved');
    $translation->render();
    enqueueScriptFile(ModuleHelper::buildRessourcePath('core_settings', 'js/design.js'));
    combinedScriptHtml();
}
