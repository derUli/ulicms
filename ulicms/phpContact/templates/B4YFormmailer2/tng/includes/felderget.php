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
 * replaces for the filefield engine
 * hier können auch Replaces innnerhalb {feld:....} verwendet werden!
 */



/**
 * Kein direkter Zugang
 */
  defined( '_VALID_B4YF' ) or die( 'Restricted access' );



/**
 * Funktion die alle Parameter in ein zweidimensionales Array ladet und bei Erfolg zurückliefert
 */ 
  if (!function_exists('b4yf_getparams')) {
  
    function b4yf_getparams($b4yf_param) {
    
      global $b4yf_absolute_path, $b4yf_template;
    
        // wenn es Parameter gibt  
        if (isset($b4yf_param)) { 
          $b4yf_countParams = count($b4yf_param);

          // Array nochmals zerteilen
           $b4yf_count3 = 1; // Startwert
           while ($b4yf_count3 <= $b4yf_countParams) {
             $b4yf_arrayParams[$b4yf_count3] = explode('|',$b4yf_param[$b4yf_count3]);
             $b4yf_count3++;
          }
        }
      // }
      return $b4yf_arrayParams;
    }
  
  }



/**
 * Prüfe ob das Feld mit dem abgefragtem Label ein Pflichtfeld ist
 * Usage: b4yf_required($a,"001_LABEL");
 * Rückgabe: true (Pflichtfeld) oder false (kein Pflichtfeld)
 */
  if (!function_exists('b4yf_required')) {
  
    function b4yf_required($parameter,$label) {
      $b4yf_sum = count($parameter);
      for ($i = 1; $i <= $b4yf_sum; $i++) {
        if ( $parameter[$i][4] == "%%".$label."%%" ) {
          if ( ($parameter[$i][0] == "required" ) && ($parameter[$i][3] == "yes" ) ) {
            return true;
          }
        }
      }
      return false;
    }
  
  }





/**
 * Wildcards auslesen und automatisch durch die Sprachkonstanten ersetzen
 */
  $b4yf_tnc_file = join ('', file ($b4yf_absolute_path.'templates/'.$b4yf_template.'/htmls/formular.tpl.htm'));
  preg_match_all("#%%(.*?)%%#s", $b4yf_tnc_file, $b4yf_tnc_wildcards);
  
  if (file_exists($b4yf_absolute_path.'templates/'.$b4yf_template.'/php/settings.php')) {
    include_once ($b4yf_absolute_path.'templates/'.$b4yf_template.'/php/settings.php');
  }
  if (!isset($b4yf_required_feld_before)) { $b4yf_required_feld_before = '<span class="b4yf_required">'; }
  if (!isset($b4yf_required_feld_after)) { $b4yf_required_feld_after = '</span>'; }

  foreach($b4yf_tnc_wildcards[1] as $b4yf_tnc_wildcard) {
    if ( $b4yf_tnc_wildcard != "COPYRIGHT") {
      $param_array = b4yf_getparams($b4yf_param); // Parameter in ein Array laden
            
      if ( !b4yf_required($param_array,$b4yf_tnc_wildcard) ) {
        $b4yf_required_feld_before1 = "";
        $b4yf_required_feld_after1 = "";
      } else {
        $b4yf_required_feld_before1 = $b4yf_required_feld_before;
        $b4yf_required_feld_after1 = $b4yf_required_feld_after;
      }
      
      @$b4yf_wildcard = str_replace("%%".$b4yf_tnc_wildcard."%%", $b4yf_required_feld_before1.constant("_B4YF_".$b4yf_tnc_wildcard).$b4yf_required_feld_after1, $b4yf_wildcard);
    }
  }

/**
 * spezial Wildcards auslesen und mit der Pflichtfeldmarkierung versehen
 */
  $b4yf_tnc_file = join ('', file ($b4yf_absolute_path.'templates/'.$b4yf_template.'/htmls/formular.tpl.htm'));
  preg_match_all("#§§(.*?)§§#s", $b4yf_tnc_file, $b4yf_tnc_wildcards);
    
  $b4yf_tnc_wildcards = array_unique ($b4yf_tnc_wildcards[1]); // gleiche Einträge löschen
  
  foreach($b4yf_tnc_wildcards as $b4yf_tnc_wildcard) {
    if ( $b4yf_tnc_wildcard != "COPYRIGHT") {
      $param_array = b4yf_getparams($b4yf_param); // Parameter in ein Array laden
      
      if ( !b4yf_required($param_array,$b4yf_tnc_wildcard) ) {
        $b4yf_required_feld_before1 = "";
        $b4yf_required_feld_after1 = "";
      } else {
        $b4yf_required_feld_before1 = $b4yf_required_feld_before;
        $b4yf_required_feld_after1 = $b4yf_required_feld_after;
      }
      
      @$b4yf_wildcard = str_replace("§§".$b4yf_tnc_wildcard."§§", $b4yf_required_feld_before1."§§".$b4yf_tnc_wildcard."§§".$b4yf_required_feld_after1, $b4yf_wildcard);
    }
  }



/**
 * mobile
 * notwend um die placehoder in der Desktopversion zu entfernen
 */
  include ($b4yf_absolute_path.'templates/'.$b4yf_template.'/tng/mobile/index.php');



/**
 * Placeholder für Desktop Version deaktivieren
 */
  if ( !defined('_B4YF_MOBILECHECK') && ($b4yf_test_mobi != "1")) {   
    $b4yf_wildcard = str_replace("placeholder:", "dummy:", $b4yf_wildcard);
  }
  if ( $b4yf_post_mobi == "0" ) {   
    $b4yf_wildcard = str_replace("placeholder:", "dummy:", $b4yf_wildcard);
  }


/**
 * templatespezifische Replaces
 */
  if (file_exists($b4yf_absolute_path.'templates/'.$b4yf_template.'/php/replace_felder.php')) {
    include_once ($b4yf_absolute_path.'templates/'.$b4yf_template.'/php/replace_felder.php');
  }

?>