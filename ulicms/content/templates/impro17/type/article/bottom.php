<?php 
if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

$meta = get_article_meta(); 
?>
<?php if (StringHelper::isNotNullOrEmpty($meta->article_author_name)) { ?>
    <p class="author-info"><?php
        if (StringHelper::isNullOrEmpty($meta->article_author_email)) {
            translate("ARTICLE_WRITTEN_BY_X", array(
                "%author%" => Template::getEscape($meta->article_author_name)
            ));
        } else {
            translate("ARTICLE_WRITTEN_BY_X_WITH_LINK", array(
                "%author%" => Template::getEscape($meta->article_author_name),
                "%email%" => Template::getEscape($meta->article_author_email)
            ));
        }
        ?></p>
    <?php
}

require_once getTemplateDirPath(get_theme()) . "/bottom.php";
