<?php


  if(!function_exists("cleanString")){
function cleanString($string, $separator = '-'){
	$accents = array('Š' => 'S', 'š' => 's', 'Ð' => 'Dj','Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss','à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f', 'Ä' => 'Ae', 'ä' => 'ae', 
	'Ö' => 'Oe', 'ö' => 'oe', 'Ü' => 'Ue', 'ü' => 'ue', 'ß' => 'ss'
	);
	$string = strtr($string, $accents);
	$string = strtolower($string);
	$string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
	$string = preg_replace('{ +}', ' ', $string);
	$string = trim($string);
	$string = str_replace(' ', $separator, $string);
 
	return $string;
}

}

  if(!function_exists("real_htmlspecialchars")){
function real_htmlspecialchars($string)
{
    return htmlspecialchars($string, ENT_QUOTES, "UTF-8");
}

}

/**
 * Get excerpt from string
 * 
 * @param String $str String to get an excerpt from
 * @param Integer $startPos Position int string to start excerpt from
 * @param Integer $maxLength Maximum length the excerpt may be
 * @return String excerpt
 */
function getExcerpt($str, $startPos=0, $maxLength=100) {
        $str = str_replace("&nbsp;", " ", $str);
	if(strlen($str) > $maxLength) {
		$excerpt   = substr($str, $startPos, $maxLength-3);
		$lastSpace = strrpos($excerpt, ' ');
		$excerpt   = substr($excerpt, 0, $lastSpace);
		$excerpt  .= '...';
	} else {
		$excerpt = $str;
	}
	
	return $excerpt;
}

function isEmpty($str){
   $str = trim($str);
   return empty($str);
}

function decodeHTMLEntities($str){
 return html_entity_decode($str, ENT_COMPAT, 'UTF-8');
}

// Häufigste Wörter in String ermitteln und als Assoziatives Array zurückgeben.
// z.B. für automatisches ausfüllen der Meta-Keywords nutzbar
function keywordsFromString($text) {
 $return = array();
  
  // Punkt, Beistrich, Zeilenumbruch... in Leerzeichen umwandeln
  $text = str_replace(array("\n", ".", ","), " ", $text);
  
  // text an Leerzeichen zerlegen
  $array = explode(" ", $text);

  foreach($array as $word) {
    if(strlen($word) == 0) {
      // wenn kein Wort vorhanden ist nichts machen
      continue;
    }
    if(!in_array($word, $array)) {
      // wenn das wort zum ersten mal gefunden wurde
      $return[$word] = 1; 
    } else {
      // wenn schon vorhanden
      $return[$word]++;
    }
  }
  
    
  $return = array_filter($return, "decodeHTMLEntities");
  // nach häufigkeit sortieren
  arsort($return);

    // array zurückgeben
  return $return;
}

?>
