<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');
?>
<a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('info'); ?>"
   class="btn btn-light btn-back is-ajax"
   ><i class="fa fa-arrow-left"></i>
    <?php translate('back'); ?></a>

<h1>
    <?php translate('changelog'); ?>
</h1>
<div class="changelog">
    <?php
    $controller = new InfoController();
echo $controller->_fetchChangelog();
?>
</div>
