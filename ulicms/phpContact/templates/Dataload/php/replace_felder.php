<?php
/**
 * @version   v2.5 - 26.05.2013
 * @package   phpContact 1.3.0 - Template: Dataload
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
 * alle verfügbaren Variablen aus der Parameterdatei auslesen
 */
  $b4yf_params_template = dirname(__FILE__)."/../php/params.php";
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
 * Attachement
 */
  $b4yf_wildcard = str_replace("§§DATEI1_FILESIZE§§", $phpContact_anhang1_size, $b4yf_wildcard);
  $b4yf_wildcard = str_replace("§ERROR_DATEI1_FILEALLOWED§§", $phpContact_anhang1_allowed, $b4yf_wildcard);
  
  if ($phpContact_anhang1_pflicht=="ja") {
    $b4yf_wildcard = str_replace("§§ERROR_DATEI1§§", _B4YF_ERROR_DATEI1, $b4yf_wildcard);
    $b4yf_wildcard = str_replace("§§DATEI1§§", "<strong>"._B4YF_DATEI1."</strong>", $b4yf_wildcard);
  } else {
    $b4yf_wildcard = str_replace("§§ERROR_DATEI1§§", "", $b4yf_wildcard);
    $b4yf_wildcard = str_replace("§§DATEI1§§", _B4YF_DATEI1, $b4yf_wildcard);
  }
  
  $b4yf_wildcard = str_replace("§§DATEI2_FILESIZE§§", $phpContact_anhang2_size, $b4yf_wildcard);
  $b4yf_wildcard = str_replace("§§ERROR_DATEI2_FILEALLOWED§§", $phpContact_anhang2_allowed, $b4yf_wildcard);


?>