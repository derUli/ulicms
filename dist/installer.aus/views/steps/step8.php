<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');
?>
<form id="setup-database" action="#" method="get">
    <div class="alert alert-warning mb-3">
        <?php echo TRANSLATION_THIS_PROCEDUDRE_WILL_TAKE_SOME_MINUTES; ?></p>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> <?php echo TRANSLATION_BUILD_DATABASE; ?></button>
</form>