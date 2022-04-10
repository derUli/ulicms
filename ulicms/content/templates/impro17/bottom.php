<?php
use UliCMS\Localization\JSTranslation;
?> 
<?php Template::comments(); ?>
<div class="advertisement">
    <?php Template::randomBanner(); ?>
</div>
</article>
</main>
<footer class="footer">
    <?php Template::footerText(); ?>
</footer>
</div>
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
