<?php

/**
 * Get excerpt from string
 * 
 * @param String $str String to get an excerpt from
 * @param Integer $startPos Position int string to start excerpt from
 * @param Integer $maxLength Maximum length the excerpt may be
 * @return String excerpt
 */
function getExcerpt($str, $startPos=0, $maxLength=100) {
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

// Häufigste Wörter in String ermitteln und als Assoziatives Array zurückgeben.
// z.B. für automatisches ausfüllen der Meta-Keywords nutzbar
function keywordsFromString($text) {
  $return = array();
  
  // Punkt, Beistrich, Zeilenumbruch... in Leerzeichen umwandeln
  $text = str_replace(array("\n", ".", ",", "!", "?", "&nbsp;"), " ", $text);
 
  
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
    // nach häufigkeit sortieren
    arsort($return);

  // array zurückgeben
  return $return;
}

?>