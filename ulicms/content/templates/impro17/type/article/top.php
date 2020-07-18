<?php
require_once getTemplateDirPath(get_theme()) . "/top.php";
$meta = get_article_meta();
$page = get_page();
$lastmodified = $page["lastmodified"];
if (!is_null($meta->article_date)) {
    $lastmodified = $meta->article_date;
}
$article_image = getTemplateDirPath("impro17") . "images/nopic.jpg";
?>
<strong><?php translate("date"); ?>:</strong>
<time datetime="<?php echo date(DATE_W3C, $lastmodified); ?>">
    <?php echo strftime("%x", $lastmodified); ?>
</time>
</p>
<?php
if ($meta && !empty($meta->article_image)) {
    $article_image = $meta->article_image;
}
?><p>
    <img src="<?php Template::escape($article_image); ?>"
         alt="<?php Template::escape($meta->title); ?>" class="article-image">
</p>