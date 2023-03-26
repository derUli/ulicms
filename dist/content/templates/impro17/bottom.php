<?php Template::comments(); ?>
<div class="advertisement">
    <?php random_banner(); ?>
</div>
</main>
</div>
<footer class="footer">
    <?php Template::footerText(); ?>
</footer>
<?php Template::footer(); ?>
</div>
<?php
$translation = new \App\Translations\JSTranslation();
$translation->addKey('menu');
$translation->renderJS();

enqueueScriptFile(getTemplateDirPath(get_theme()) . 'main.js');
combinedScriptHtml();
?>
</body>
</html>
