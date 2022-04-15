<?php

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Localization\JSTranslation;
?> 
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
    <?php echo Template::editButton(); ?>
</footer>
<?php Template::footer(); ?>
</div>
<?php
$translation = new JSTranslation();
$translation->addKey("menu");
$translation->renderJS();

enqueueScriptFile(getTemplateDirPath(get_theme()) . "scripts/functions.js");
enqueueScriptFile(getTemplateDirPath(get_theme()) . "scripts/navigation.js");
enqueueScriptFile(getTemplateDirPath(get_theme()) . "scripts/headline.js");
enqueueScriptFile(getTemplateDirPath(get_theme()) . "scripts/fx.js");
combinedScriptHtml();
?>
</body>
</html>