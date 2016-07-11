<?php
$id = get_ID ();
if ($id !== null) {
	$list = new List_Data ( $id );
	if ($list->content_id !== null) {
		$entries = $list->filter ();
		if (count ( $entries ) > 0) {
			// Pagination
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
			?>
<ol class="ulicms-content-list">
	<?php
			
			foreach ( $entries as $entry ) {
				?>
	<li><a
		href="<?php Template::escape(buildSEOUrl($entry->systemname));?>"><?php Template::escape($entry->title)?></a></li>
	<?php }?>
	</ol>
<?php
		}
		
		?>
	<?php
	}
}