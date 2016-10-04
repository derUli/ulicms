<?php
$top_file = getTemplateDirPath ( get_theme () ) . "oben.php";
include $top_file;

$article_meta = get_article_meta ();
if (isNotNullOrEmpty ( $article_meta ) and isNotNullOrEmpty ( $article_meta->article_image )) {
	?>
<div id="main-article-image">
<img src="<?php Template::escape($article_meta->article_image)?>"
	alt="<?php headline();?>"></div>
<?php
}
