<?php
$article_meta = get_article_meta ();
if (isNotNullOrEmpty ( $article_meta )) {
	?>
<div id="article-meta">
	<small><?php
	
	if (isNotNullOrEmpty ( $article_meta->article_author_name )) {
		echo Template::escape ( $article_meta->article_author_name );
	} else {
		translate ( "guest" );
	}
	if (isNotNullOrEmpty ( $article_meta->article_date )) {
		?>
	<?php translate("on_time");?>
		<?php echo strftime ( "%A, %x %X", strtotime ( $article_meta->article_date ) );
		?>
		
		<?php translate("o_clock");?>
	 
<?php }?>
	</small>
<?php }?>
</div>
<?php
$bottom_file = getTemplateDirPath ( get_theme () ) . "unten.php";
include $bottom_file;