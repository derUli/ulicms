<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');
?>

<p><?php
    echo TRANSLATION_SELECT_LANGUAGE;
$language = InstallerController::getLanguage();
?></p>
<div class="form-group mb-3">
    <select name='language' id='language' class="form-control">
        <option value="de" <?php
    if ($language == 'de') {
        echo 'selected';
    }
?>>Deutsch</option>

        <option value="en" <?php
if ($language == 'en') {
    echo 'selected';
}
?>>English</option>
    </select>
</div>
<div>
    <a href="index.php?step=2" class="btn btn-primary"><i class="fas fa-check"></i> <?php echo TRANSLATION_NEXT; ?></a>
</div>