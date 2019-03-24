<?php Template::comments(); ?>
<div class="advertisement">
    <?php random_banner(); ?>
</div>
</main>
</div>
<footer class="footer">
    <p>&copy;
        <?php if (date("Y") > 2016) { ?>
            2016 -
        <?php } ?>
        <?php year(); ?> by <?php homepage_owner(); ?>
    </p>
</footer>
<?php Template::footer(); ?>
</div>
<?php
$translation = new JSTranslation();
$translation->addKey("menu");
$translation->renderJS();

enqueueScriptFile(getTemplateDirPath(get_theme()) . "main.js");
combinedScriptHtml();
?>
</body>
</html>
<!--  <?php echo date("r"); ?>-->
