<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');
?>
<form role="form" method="post" action="index.php?submit_form=Demodata">

    <div class="form-group">
        <div class="checkbox">
            <label><input type="checkbox" value="yes" name="install_demodata"
                          id="install_demodata"
                          <?php
                          if (! empty($_SESSION['install_demodata'])) {
                              echo 'checked';
                          }
?>><?php echo TRANSLATION_INSTALL_DEMO_DATA; ?></label>
        </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> <?php echo TRANSLATION_APPLY; ?></button>
</form>