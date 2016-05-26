<?php
$id = get_ID ();
if ($id !== null) {
	$list = List_Data ( $id );
	if($list->content_id !== null){
		$entries = $list->filter();
		if(count($entries) > 0){
	?>
	<ol class="ulicms-content-list">
	<?php foreach($entries as $entry){?>
	<?php }?>
	</ol>
	<?php }
	
	
	?>
	<?php
}
}