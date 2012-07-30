<?php 
// Displays news on single page
function news_render(){
	ob_start();
	news();
	$html_output = ob_get_clean();
	return $html_output;
}
?>