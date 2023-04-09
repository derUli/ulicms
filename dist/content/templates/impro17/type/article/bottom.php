<?php $meta = get_article_meta(); ?>
<?php if (!empty($meta->article_author_name)) { ?>
    <p class="author-info"><?php
        if (empty($meta->article_author_email)) {
            translate('ARTICLE_WRITTEN_BY_X', [
                '%author%' => Template::getEscape($meta->article_author_name)
            ]);
        } else {
            translate('ARTICLE_WRITTEN_BY_X_WITH_LINK', [
                '%author%' => Template::getEscape($meta->article_author_name),
                '%email%' => Template::getEscape($meta->article_author_email)
            ]);
        }
    ?></p>
<?php } ?>
<?php
require_once getTemplateDirPath(get_theme()) . '/bottom.php';
