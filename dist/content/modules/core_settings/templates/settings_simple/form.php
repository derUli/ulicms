<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;
use App\Translations\JSTranslation;

$permissionChecker = PermissionChecker::fromCurrentUser();

$controller = ControllerRegistry::get();
$allSettings = Settings::getAll();
$settings = [];
foreach ($allSettings as $option) {
    $settings[$option->name] = Template::getEscape($option->value);
}
?>
<p>
    <a
        href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('settings_categories'); ?>"
        class="btn btn-light btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
</p>
<h2><?php translate('general_settings'); ?></h2>
<?php
echo \App\Helpers\ModuleHelper::buildMethodCallForm('SimpleSettingsController', 'save', [], 'post', [
    'id' => 'settings_simple',
    'class' => 'ajax-form'
]);
?>
<table>
    <tr>
        <td><strong><?php translate('homepage_title'); ?></strong></td>
        <td><a href="index.php?action=homepage_title" class="btn btn-light is-not-ajax"
                >
                <i
                    class="fa fa-edit"></i> <?php translate('edit'); ?>
            </a></td>
    </tr>
    <tr>
        <td><strong><?php translate('site_slogan'); ?></strong></td>
        <td><a
                href="index.php?action=site_slogan"
                class="btn btn-light is-not-ajax"
                ><i
                    class="fa fa-edit"></i> <?php translate('edit'); ?></a></td>
    </tr>
    <tr>
        <td><strong><?php translate('homepage_owner'); ?></strong></td>
        <td><input type="text" name="homepage_owner" class="form-control"
                    value="<?php echo $settings['homepage_owner']; ?>"></td>
    </tr>
    <tr>
        <td><strong><?php translate('OWNER_MAILADRESS'); ?></strong></td>
        <td><input type="email" name="email" class="form-control"
                    value="<?php echo $settings['email']; ?>"></td>
    </tr>
    <tr>
        <td><strong><?php translate('frontpage'); ?></strong></td>
        <td><a href="index.php?action=frontpage_settings"
                class="btn btn-light is-not-ajax"><i class="fa fa-edit"></i> <?php translate('edit'); ?></a></td>
    </tr>
    <?php if ($permissionChecker->hasPermission('error_pages')) {
        ?>
        <tr>
            <td><strong><?php translate('error_pages'); ?></strong></td>
            <td><a href="index.php?action=error_pages"
                    class="btn btn-light is-not-ajax"><i class="fa fa-edit"></i> <?php translate('edit'); ?></a></td>
        </tr>
    <?php }
    ?>
    <tr>
        <td><strong><?php translate('MAINTENANCE_MODE_ENABLED'); ?></strong></td>
        <td><input type="checkbox" name='maintenance_mode'
                    class="js-switch"
                    <?php
                    if ((bool)$settings['maintenance_mode']) {
                        echo ' checked';
                    }
?>></td>
    </tr>
    <tr>
        <td><strong><?php translate('GUEST_MAY_REGISTER'); ?></strong></td>
        <td><input type="checkbox" name="visitors_can_register"
                    class="js-switch"
                    <?php
if (strtolower($settings['visitors_can_register'] == 'on') || $settings['visitors_can_register'] == '1' || strtolower($settings['visitors_can_register']) == 'true') {
    echo ' checked';
}
?>></td>
    </tr>
    <tr>
        <td>
            <strong><?php translate('enable_password_reset'); ?></strong>
        </td>
        <td><input type="checkbox" name="disable_password_reset"
                    value="enable"
                    class="js-switch"

                    <?php
if (! isset($settings['disable_password_reset'])) {
    echo ' checked';
}
?>>
        </td>
    </tr>
    <tr>
        <td><strong><?php translate('timezone'); ?></strong></td>
        <td><?php echo $controller->getTimezones();?></td>
    </tr>
    <tr>
        <td><strong><?php translate('search_engines'); ?></strong></td>
        <td style="width:50%">
        <select name="robots" size=1 class="form-control form2">
                <?php
if (Settings::get('robots') == 'noindex,nofollow') {
    ?>

                    <option value="index,follow"><?php translate('SEARCH_ENGINES_INDEX'); ?></option>
                    <option value="noindex,nofollow" selected><?php translate('SEARCH_ENGINES_NOINDEX'); ?></option>

                    <?php
} else {
    ?>
                    <option value="index,follow" selected><?php translate('SEARCH_ENGINES_INDEX'); ?></option>
                    <option value="noindex,nofollow"><?php translate('SEARCH_ENGINES_NOINDEX'); ?></option>
                <?php }
?>
            </select></td>
    </tr>
        <tr>
        <td></td>
        <td>
            <strong><?php translate('metadata'); ?></strong></strong></td>
    </tr>
    <tr>
        <td><strong><?php translate('description'); ?></strong></td>
        <td><a href="index.php?action=meta_description"
                class="btn btn-light is-not-ajax"><i class="fa fa-edit"></i> <?php translate('edit'); ?></a></td>
    </tr>
    <?php
    if ($permissionChecker->hasPermission('open_graph')) {
        ?>
        <tr>
            <td><strong><?php translate('open_graph'); ?>
                </strong></td>
            <td><a href="index.php?action=open_graph" class="btn btn-light is-not-ajax"><i
                        class="fa fa-edit"></i> <?php translate('edit'); ?></a></td>
        </tr>
    <?php }
    ?>
    <?php do_event('settings_simple'); ?>
    <tr>
        <td>
        <td style="text-align: center">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> <?php translate('save_changes'); ?></button>
        </td>
    </tr>
</table>
<input type="hidden" name="save_settings" value="save_settings">

<?php
echo \App\Helpers\ModuleHelper::endForm();

$translation = new JSTranslation();
$translation->addKey('changes_were_saved');
$translation->render();

enqueueScriptFile(\App\Helpers\ModuleHelper::buildRessourcePath('core_settings', 'js/ajax_form.js'));
combinedScriptHtml();
