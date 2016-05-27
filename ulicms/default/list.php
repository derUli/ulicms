<?php
$id = get_ID ();
if ($id !== null) {
	$list = new List_Data ( $id );
	if ($list->content_id !== null) {
		$entries = $list->filter ();
		if (count ( $entries ) > 0) {
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