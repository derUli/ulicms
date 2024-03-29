<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;

$permissionChecker = new PermissionChecker(get_user_id());
?>
<h1><?php translate('settings'); ?></h1>
<?php
if ($permissionChecker->hasPermission('settings_simple')
        || $permissionChecker->hasPermission('design')
        || $permissionChecker->hasPermission('spam_filter')
        || $permissionChecker->hasPermission('privacy_settings')
        || $permissionChecker->hasPermission('performance_settings')
        || $permissionChecker->hasPermission('community_settings')
        || $permissionChecker->hasPermission('cache')
        || $permissionChecker->hasPermission('motd')
        || $permissionChecker->hasPermission('logo')
        || $permissionChecker->hasPermission('languages')
        || $permissionChecker->hasPermission('other')) {
    ?>
    <div class="button-menu">
        <?php
        if ($permissionChecker->hasPermission('settings_simple')) {
            ?>
            <a href="index.php?action=settings_simple" class="btn btn-light is-not-ajax"><i
                    class="fas fa-tools"></i> <?php translate('general_settings'); ?></a>                         <?php }
        ?>
            <?php
            if ($permissionChecker->hasPermission('design')) {
                ?>
            <a href="index.php?action=design" class="btn btn-light is-not-ajax"><i
                    class="fas fa-tools"></i> <?php translate('design'); ?></a> 
            <?php }
            ?>
            <?php
            if ($permissionChecker->hasPermission('spam_filter')) {
                ?>
            <a href="index.php?action=spam_filter" class="btn btn-light is-not-ajax"><i
                    class="fas fa-tools"></i> <?php translate('spamfilter'); ?></a>                         <?php }
            ?>
            <?php
            if ($permissionChecker->hasPermission('privacy_settings')) {
                ?>
            <a href="?action=privacy_settings" class="btn btn-light is-not-ajax"><i
                    class="fas fa-tools"></i> <?php translate('privacy'); ?></a>
            <?php }
            ?>
            <?php
            if ($permissionChecker->hasPermission('performance_settings')) {
                ?>
            <a
                href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('performance_settings'); ?>"
                class="btn btn-light is-not-ajax"><i class="fas fa-tools"></i> <?php translate('performance'); ?></a> 
            <?php }
            ?>
            <?php
            if ($permissionChecker->hasPermission('community_settings')) {
                ?>
            <a
                href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('community_settings'); ?>"
                class="btn btn-light is-not-ajax"><i class="fas fa-tools"></i> <?php translate('comments'); ?></a> 
            <?php }
            ?>
            <?php
            if ($permissionChecker->hasPermission('motd')) {
                ?>
            <a href="index.php?action=motd" class="btn btn-light is-not-ajax"><i
                    class="fas fa-tools"></i> <?php translate('motd'); ?></a> 
            <?php }
            ?>
            <?php
            if ($permissionChecker->hasPermission('languages')) {
                ?>
            <a href="index.php?action=languages" class="btn btn-light is-not-ajax"><i
                    class="fas fa-tools"></i> <?php translate('languages'); ?></a> 
            <?php }
            ?>
            <?php
            if ($permissionChecker->hasPermission('other')) {
                ?>
            <a href="?action=other_settings" class="btn btn-light is-not-ajax"><i
                    class="fas fa-tools"></i> <?php translate('other'); ?></a>
            <?php }
            ?>
    </div>
    <?php
} else {
    noPerms();
}
