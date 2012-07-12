<?php 
function phpinfo_render(){
	ob_start();
	phpinfo();
	$html_output = ob_get_clean();

	# Body-Content rausholen
	$html_output = preg_replace('#^.*<body>(.*)</body>.*$#s', '$1', $html_output);
	$html_output= preg_replace('#>(on|enabled|active)#i', '><span style="color:#090">$1</span>', $html_output);
	$html_output = preg_replace('#>(off|disabled)#i', '><span style="color:#f00">$1</span>', $html_output);
	$html_output = str_replace('<font', '<span', $html_output);
	$html_output = str_replace('</font>', '</span>', $html_output);
	return $html_output;
	
}
?>