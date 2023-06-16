<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');
?>
<form action="index.php" method="get">
    <input type="hidden" name="action" value="module_settings"> <input
        type="hidden" name="module" value="fortune2">
    <button type="submit" class="btn btn-light">Reset</button>
</form>
<br />

<form action="index.php" method="get">
    <input type="hidden" name="action" value="module_settings"> <input
        type="hidden" name="module" value="fortune2"> <input type="hidden"
        name="sClass" value="Fortune"> <input type="hidden" name="sMethod"
        value="doSomething">
    <button type="submit" class="btn btn-light">GET</button>
</form>
<br />
<form
    action="<?php Template::escape(\App\Helpers\ModuleHelper::buildAdminURL('fortune2', 'sClass=Fortune&sMethod=doSomething')); ?>"
    method="post">
        <?php csrf_token_html(); ?>
    <button type="submit" class="btn btn-light">POST</button>
</form>
<br />
<code><?php if (\App\Storages\ViewBag::get('sample_text')) { ?>
        <?php Template::escape(\App\Storages\ViewBag::get('sample_text')); ?>
    <?php } ?></code>
