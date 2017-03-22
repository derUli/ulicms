<?php
include_once getTemplateDirPath(get_theme()). "/top.php";
$meta = get_article_meta();
$page = get_page();
$lastmodified = $page["lastmodified"];
$article_image = getTemplateDirPath("2018") . "images/nopic.jpg";
?>

<?php if($meta and !empty($meta->article_image)){
			$article_image = $meta->article_image;
		}
	
			?><p><img src="<?php Template::escape($article_image);?>" alt="<?php Template::escape($meta->title);?>" class="article-image">
		</p>
<p><strong><?php translate("date");?>:</strong> <?php echo strftime("%x", $lastmodified);?></p>