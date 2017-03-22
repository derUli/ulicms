<?php
$id = get_ID ();
if ($id !== null) {
	$list = new List_Data ( $id );
	if ($list->content_id !== null) {
		$entries = $list->filter ();
		if (count ( $entries ) > 0) {
			// Pagination
			$entries_count_total = count ( $entries );
			$use_pagination = $list->use_pagination;
			$start = 0;
			$limit = intval ( $list->limit );
			if ($limit > 0 and $use_pagination) {
				if (isset ( $_GET ["start"] )) {
					$start = intval ( $_GET ["start"] );
				}
				$entries = array_slice ( $entries, $start, $limit );
				$entries_count = count ( $entries );
			}

			$previous_start = $start - $limit;
			if ($previous_start < 0) {
				$previous_start = 0;
			}

			$next_start = $start + $limit;
			if ($next_start <= $entries_total_count) {
				$next_start = $start + $limit;
			}

			?>

<div class="container">
	<?php

			foreach ( $entries as $entry ) {
				$article_image = getTemplateDirPath ( "2018" ) . "images/nopic.jpg";
				$meta = get_article_meta ( $entry->systemname );
				$excerpt = strip_tags ( $meta->excerpt, "<img><iframe><embed><object>" );
				$excerpt = trim ( $excerpt );
				$excerpt = isNotNullOrEmpty ( $excerpt ) ? $excerpt : $entry->content;
				?>
  <div class="row article-list-row">
		<p>
			<strong><a
				href="<?php Template::escape(buildSEOUrl($entry->systemname));?>"><?php Template::escape($entry->title);?></a></strong>
		</p>

		<div class="col-sm-5">
		<?php

				if ($meta and ! empty ( $meta->article_image )) {
					$article_image = $meta->article_image;
				}
				?>
		<p>
				<a href="<?php Template::escape(buildSEOUrl($entry->systemname));?>"
					class="list-article-image"><img src="<?php echo $article_image;?>"
					alt="Screenshot <?php Template::escape($entry->title);?>"
					class="article-image"></a>
			</p>

		</div>

		<div class="col-sm-7">

	<?php echo $excerpt;?>
	<p>
				<a href="<?php Template::escape(buildSEOUrl($entry->systemname));?>"><?php translate("readmore");?></a>
			</p>
		</div>
	</div>
	<?php }?>

<div class="bottom-list-border"></div>
<?php if($use_pagination){?>
<div class="page_older_newer">
<?php if($start > 0 and $use_pagination){?>
<span class="blog_pagination_newer"><a
			href="<?php Template::escape(buildSEOUrl());?>?start=<?php echo $previous_start;?>"><?php Template::escape("<<");?></a></span>
<?php }?>
	<?php if($start + $limit < $entries_count_total and $use_pagination){?>
<span class="blog_pagination_older"><a
			href="<?php Template::escape(buildSEOUrl());?>?start=<?php echo $next_start;?>"><?php Template::escape(">>");?></a></span>

<?php }?>
</div>

	<div class="text-right rss-icon">
		<a class="fa fa-rss fa-4x"
			href="<?php Template::escape(buildSEOUrl(false, null, "rss"));?>"
			title="Newsfeed"></a>
	</div>
<?php }?>
	<?php
		}
	}
}
