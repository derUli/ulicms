<?php

use UliCMS\Helpers\NumberFormatHelper;

$permissionChecker = new ACL();

$controller = ControllerRegistry::get("HomeController");
$model = $controller->getModel();
?>
<table>
    <?php
    $installed_at = Settings::get("installed_at");
    if ($installed_at) {
        $time = time() - $installed_at;
        $formatted = NumberFormatHelper::formatTime($time);
        ?>
        <tr>
            <td><?php translate("site_online_since"); ?></td>
            <td><?php
                echo $formatted;
                ?></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <td><?php translate("pages_count"); ?>
        </td>
        <td><?php echo $model->contentCount; ?></td>
    </tr>
    <tr>
        <td><?php translate("REGISTERED_USERS_COUNT"); ?>
        </td>
        <td><?php echo count(getUsers()) ?></td>
    </tr>
    <?php
    if (Settings::get("contact_form_refused_spam_mails") !== false) {
        ?>
        <tr>
            <td><?php echo translate("BLOCKED_SPAM_MAILS"); ?></td>
            <td><?php echo Settings::get("contact_form_refused_spam_mails") ?></td>
        </tr>
        <?php
    }
    ?>
    <?php
    if (!is_null($model->guestbookEntryCount)) {
        ?>
        <tr>
            <td><?php translate("GUESTBOOK_ENTRIES"); ?></td>
            <td><?php echo $model->guestbookEntryCount; ?></td>
        </tr>
        <?php
    }
    ?>
</table>

