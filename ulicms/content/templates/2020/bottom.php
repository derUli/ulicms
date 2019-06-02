<?php
enqueueScriptFile(
        getTemplateDirPath(
                get_theme()
        ) .
        "node_modules/onepage-scroll/jquery.onepage-scroll.min.js");
enqueueScriptFile(
        getTemplateDirPath(
                get_theme()
        ) .
        "js/main.js");
Template::footer();
?>
</body>
</html>
