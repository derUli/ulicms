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
			if($previous_start < 0){
				$previous_start = 0;
			}

			$next_start = $start + $limit;
			if($next_start > $entries_total_count){
				$previous_start = $entries_total_count - $limit;
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