<?php Template::comments(); ?>
</article>

<div class="advertisement">
    <?php Template::randomBanner(); ?>
</div>

<footer class="footer">
    <?php Template::footerText(); ?>
</footer>
<?php Template::footer(); ?>
</div>
<?php
$translation = new JSTranslation();
$translation->addKey("menu");
$translation->renderJS();

enqueueScriptFile(getTemplateDirPath(get_theme(), true) . "scripts/navigation.js");
combinedScriptHtml();
?>
</body>
</html>