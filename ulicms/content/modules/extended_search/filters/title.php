<?php
function extended_search_title_filter($title) {
	if (! empty ( $_GET ["q"] )) {
		return get_translation ( "search_results_for_x", array (
				"%subject%" => Template::getEscape ( $_GET ["q"] ) 
		) );
	} else {
		return $title;
	}
}