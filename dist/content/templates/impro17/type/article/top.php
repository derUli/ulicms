<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Helpers\DateTimeHelper;

require_once getTemplateDirPath(get_theme()) . '/top.php';
$meta = get_article_meta();
$title = get_headline();

$page = get_page();
$lastmodified = $meta->article_date ?? $page['lastmodified'];

$article_image = getTemplateDirPath('impro17') . 'images/nopic.jpg';
?>
<strong><?php translate('date'); ?>:</strong>
<time datetime="<?php echo date(DATE_W3C, $lastmodified); ?>">
    <?php echo DateTimeHelper::timestampToFormattedDateTime($lastmodified, IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE); ?>
</time>
</p>
<?php
if ($meta && ! empty($meta->article_image)) {
    $article_image = $meta->article_image;
}
?><p>
    <img src="<?php Template::escape($article_image); ?>"
         alt="<?php Template::escape($title); ?>" class="article-image">
</p>