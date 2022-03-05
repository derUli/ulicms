<?php Template::comments(); ?>
</article>


<div class="advertisement">
    <?php Template::randomBanner(); ?>
</div>
</main>
<footer class="footer">
<div>
    <?php Template::footerText(); ?>
</div>
<?php echo Template::editButton();?>
</footer>
<?php Template::footer(); ?>
</div>
<?php
$translation = new JSTranslation();
$translation->addKey("menu");
$translation->renderJS();

enqueueScriptFile(getTemplateDirPath(get_theme(), true) . "scripts/navigation.js");
enqueueScriptFile(getTemplateDirPath(get_theme(), true) . "scripts/headline.js");
combinedScriptHtml();
?>
</body>
</html>