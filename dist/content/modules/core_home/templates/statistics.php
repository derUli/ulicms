<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Helpers\NumberFormatHelper;

$controller = ControllerRegistry::get('HomeController');
$model = $controller->getModel();

$installed_at = Settings::get('installed_at');

?>
<table class="table">
<tbody>
    <?php
        if ($installed_at) {
            $formatted = NumberFormatHelper::formatTime($installed_at);
            ?>
                <tr>
                    <td><?php translate('site_online_since'); ?></td>
                    <td><?php echo $formatted; ?></td>
                </tr>
                <?php
        }
?>
        <tr>
            <td><?php translate('pages_count'); ?>
            </td>
            <td><?php echo $model->contentCount; ?></td>
        </tr>
        <tr>
            <td><?php translate('REGISTERED_USERS_COUNT'); ?>
            </td>
            <td><?php echo count(getUsers()); ?></td>
        </tr>
        <?php
if (Settings::get('contact_form_refused_spam_mails')) {
    ?>
            <tr>
                <td><?php translate('BLOCKED_SPAM_MAILS'); ?></td>
                <td><?php echo Settings::get('contact_form_refused_spam_mails'); ?></td>
            </tr>
            <?php
}
?>
</tbody>
</table>

