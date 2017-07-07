<?php
/**
 * @version   v1.0 - 06.04.2013
 * @package   TNG Framework
 * @author    Günther Hörandl <info@phpcontact.net>
 * @copyright Copyright (c) 2009 - 2013 by Günther Hörandl
 * @license   http://www.phpcontact.net/license.html, see LICENSE.txt
 * @link      http://www.phpcontact.net/
 */

/**
 * Diese Datei wird direkt in die Template-Engine geladen
 * PHP Werte / Variable können somit direkt in das Template eingebaut werden
 */



/**
 * Kein direkter Zugang
 */
  defined( '_VALID_B4YF' ) or die( 'Restricted access' );



/**
 * Sprachdatei laden
 */
  $b4yf_language_file_template = dirname(__FILE__).'/../../language/deutsch.ini';
  if (file_exists($b4yf_language_file_template)) {
    $b4yf_language_file = file($b4yf_language_file_template);
    array_unique($b4yf_language_file);
    foreach ($b4yf_language_file as $b4yf_line_num => $b4yf_line) {
      $b4yf_line=trim($b4yf_line);
      if ($b4yf_line=="") continue;
      if (strpos($b4yf_line,";")===0) continue;
      $b4yf_a = explode('=', $b4yf_line, 2);
      if (isset($b4yf_a[0]) && isset($b4yf_a[1])) {
        if (!defined("_B4YF_".trim($b4yf_a[0]))) define("_B4YF_".trim($b4yf_a[0]),trim($b4yf_a[1]));
      }
    }
  }



/**
 * alle verfügbaren Variablen aus der Parameterdatei auslesen
 * notwendig für die Replaces der E-Mail
 */
  $b4yf_params_template = dirname(__FILE__)."/../../php/params.php";
  if (file_exists($b4yf_params_template)) {
    include($b4yf_params_template);
    $b4yf_ParamsCode = "";
    $b4yf_countParams = count($b4yf_param);
    $b4yf_count = 1;
    while ($b4yf_count <= $b4yf_countParams) {
      $b4yf_arrayParams[$b4yf_count] = explode('|',$b4yf_param[$b4yf_count]);
      if (strstr($b4yf_arrayParams[$b4yf_count][1], 'var:')) {
        $b4yf_variableName = str_replace( "var:", "", $b4yf_arrayParams[$b4yf_count][1] );
        $b4yf_ParamsCode .=  '$'.$b4yf_variableName.'="'.$b4yf_arrayParams[$b4yf_count][3].'";';
      }
      $b4yf_count++;
    }
    eval($b4yf_ParamsCode);
  }



/**
 * Wildcards aus der Parameterdatei auslesen und automatisch durch die Sprachkonstanten ersetzen
 */
  $b4yf_tnc_file = join ('', file (dirname(__FILE__).'/../../php/params.php'));
  preg_match_all("#%%(.*?)%%#s", $b4yf_tnc_file, $b4yf_tnc_wildcards);
  foreach($b4yf_tnc_wildcards[1] as $b4yf_tnc_wildcard) {
    if ( ($b4yf_tnc_wildcard != "COPYRIGHT") && // Copyright nicht rauslöschen
         ($b4yf_tnc_wildcard != "TIME-TO-UNLOCK") ) { // Reloadsperre nicht rauslöschen
      @$b4yf_wildcard = str_replace("%%".$b4yf_tnc_wildcard."%%", constant("_B4YF_".$b4yf_tnc_wildcard), $b4yf_wildcard);
    }
  }

?>