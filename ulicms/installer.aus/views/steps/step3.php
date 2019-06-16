<p>
    <?php echo TRANSLATION_FOLLOW_INSTRUCTIONS; ?>
</p>
<?php echo TRANSLATION_CHMOD; ?>
<h3>
    <?php echo TRANSLATION_PERMISSION; ?>
</h3>
<p>
    <img
        src="media/chmod_<?php
        echo htmlspecialchars(InstallerController::getLanguage());
        ?>.png"
        alt="<?php echo TRANSLATION_PERMISSIONS2; ?>"
        title="<?php echo TRANSLATION_PERMISSIONS2; ?>" border="1" />
</p>

<p>
    <a href="index.php?step=4" class="btn btn-primary"><i class="fas fa-check"></i> <?php echo TRANSLATION_NEXT; ?></a>
</p>
