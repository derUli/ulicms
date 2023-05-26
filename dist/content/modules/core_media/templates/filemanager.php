<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');

echo Template::executeModuleTemplate('core_media', 'icons.php');
?>
<h2>
    <?php translate('media'); ?>
</h2>
<iframe src="fm/dialog.php" class="fm"></iframe>