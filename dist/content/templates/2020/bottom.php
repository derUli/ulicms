<?php
enqueueScriptFile(
    getTemplateDirPath(
        get_theme()
    ) .
    "node_modules/fullpage.js/dist/fullpage.min.js"
);
enqueueScriptFile(
    getTemplateDirPath(
        get_theme()
    ) .
    "js/main.js"
);
Template::footer();
?>
</body>
</html>
